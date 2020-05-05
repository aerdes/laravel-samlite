<?php

namespace Aerdes\LaravelSamlite\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class SamlSetupCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saml:setup {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set a self-signed private key and certificate for SAML';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        [$privkey_string, $x509_string] = $this->generateKeyAndCert();

        if (! $this->updateEnvironmentFile($privkey_string, $x509_string)) {
            return;
        }

        config(['saml.sp.privateKey' => $privkey_string]);
        config(['saml.sp.x509cert' => $x509_string]);

        $this->info('Private key and certificate for SAML set successfully.');
    }

    protected function updateEnvironmentFile($privkey_string, $x509_string)
    {
        $currentKey = config('saml.sp.privateKey');
        $currentCert = config('saml.sp.x509cert');

        if ((strlen($currentKey) !== 0 or strlen($currentCert) !== 0) !== 0 && (! $this->confirmToProceed())) {
            return false;
        }

        // Replace entries if exists
        file_put_contents($this->laravel->environmentFilePath(), preg_replace(
            $this->keyReplacementPattern(),
            'SAML_SP_PRIVATE_KEY='.$privkey_string,
            file_get_contents($this->laravel->environmentFilePath())
        ));
        file_put_contents($this->laravel->environmentFilePath(), preg_replace(
            $this->certReplacementPattern(),
            'SAML_SP_CERT='.$x509_string,
            file_get_contents($this->laravel->environmentFilePath())
        ));

        // Otherwise add entries
        if (! strpos(file_get_contents($this->laravel->environmentFilePath()), 'SAML_SP_PRIVATE_KEY')) {
            file_put_contents($this->laravel->environmentFilePath(),
                file_get_contents($this->laravel->environmentFilePath())."\nSAML_SP_PRIVATE_KEY=".$privkey_string);
        }
        if (! strpos(file_get_contents($this->laravel->environmentFilePath()), 'SAML_SP_CERT')) {
            file_put_contents($this->laravel->environmentFilePath(),
                file_get_contents($this->laravel->environmentFilePath())."\nSAML_SP_CERT=".$x509_string);
        }

        return true;
    }

    protected function generateKeyAndCert()
    {
        $privkey = openssl_pkey_new();
        $dn = [
            'organizationName' => config('app.name'),
            'commonName' => config('app.url'),
        ];
        $csr = openssl_csr_new($dn, $privkey, ['digest_alg' => 'sha256']);
        $x509 = openssl_csr_sign($csr, null, $privkey, $days = 3650, ['digest_alg' => 'sha256']);
        openssl_pkey_export($privkey, $privkey_string);
        openssl_x509_export($x509, $x509_string);
        $privkey_string = str_replace(["\n", "\r", '-----BEGIN PRIVATE KEY-----', '-----END PRIVATE KEY-----'], '', $privkey_string);
        $x509_string = str_replace(["\n", "\r", '-----BEGIN CERTIFICATE-----', '-----END CERTIFICATE-----'], '', $x509_string);

        return [$privkey_string, $x509_string];
    }

    protected function keyReplacementPattern()
    {
        $escaped = preg_quote('='.config('saml.sp.privateKey'), '/');

        return "/^SAML_SP_PRIVATE_KEY{$escaped}/m";
    }

    protected function certReplacementPattern()
    {
        $escaped = preg_quote('='.config('saml.sp.x509cert'), '/');

        return "/^SAML_SP_CERT{$escaped}/m";
    }
}

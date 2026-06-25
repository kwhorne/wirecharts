<?php

namespace WireCharts\Licensing;

/**
 * Verifies WireCharts Pro license keys offline.
 *
 * A license key is "<base64url(payload)>.<base64url(signature)>" where the
 * signature is an Ed25519 signature of the payload JSON, produced by the
 * licensing server with the matching secret key. Verification is done with
 * the embedded public key, so it works fully offline.
 */
class License
{
    /**
     * Ed25519 public key (hex). The matching secret key lives only on the
     * licensing server and is never shipped with the package.
     */
    public const PUBLIC_KEY = '795e41912766c01809decb78896d7cc733c7c5520ebd3fbbf66d85dcf99b8cd9';

    protected ?array $claims = null;

    protected bool $resolved = false;

    public function __construct(
        protected ?string $key = null,
        protected ?string $host = null,
    ) {
    }

    /**
     * Is a valid, non-expired Pro license present?
     */
    public function active(): bool
    {
        return $this->claims() !== null;
    }

    /**
     * May the given component be rendered? Basics are always allowed.
     */
    public function allows(string $component): bool
    {
        return Catalog::isFree($component) || $this->active();
    }

    /**
     * The verified license claims, or null when missing/invalid/expired.
     *
     * @return array<string, mixed>|null
     */
    public function claims(): ?array
    {
        if ($this->resolved) {
            return $this->claims;
        }

        $this->resolved = true;

        return $this->claims = $this->verify($this->key);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function verify(?string $key): ?array
    {
        if (! $key || ! str_contains($key, '.')) {
            return null;
        }

        [$payloadPart, $signaturePart] = explode('.', $key, 2);

        $payloadJson = static::base64UrlDecode($payloadPart);
        $signature = static::base64UrlDecode($signaturePart);

        if ($payloadJson === false || $signature === false) {
            return null;
        }

        $publicKey = sodium_hex2bin(static::PUBLIC_KEY);

        if (strlen($signature) !== SODIUM_CRYPTO_SIGN_BYTES) {
            return null;
        }

        $valid = sodium_crypto_sign_verify_detached($signature, $payloadJson, $publicKey);

        if (! $valid) {
            return null;
        }

        $claims = json_decode($payloadJson, true);

        if (! is_array($claims)) {
            return null;
        }

        if (! $this->withinExpiry($claims) || ! $this->matchesDomain($claims)) {
            return null;
        }

        return $claims;
    }

    /**
     * @param  array<string, mixed>  $claims
     */
    protected function withinExpiry(array $claims): bool
    {
        $expires = $claims['expires'] ?? null;

        if ($expires === null) {
            return true;
        }

        return time() <= (int) $expires;
    }

    /**
     * @param  array<string, mixed>  $claims
     */
    protected function matchesDomain(array $claims): bool
    {
        $domains = $claims['domains'] ?? [];

        if (empty($domains) || $this->host === null) {
            return true;
        }

        $host = strtolower(preg_replace('/^www\./', '', $this->host));

        foreach ($domains as $domain) {
            $domain = strtolower(preg_replace('/^www\./', '', $domain));

            if ($domain === $host || $domain === '*') {
                return true;
            }

            // Wildcard subdomains: *.example.com
            if (str_starts_with($domain, '*.') && str_ends_with($host, substr($domain, 1))) {
                return true;
            }
        }

        return false;
    }

    protected static function base64UrlDecode(string $value): string|false
    {
        return base64_decode(strtr($value, '-_', '+/'), true);
    }
}

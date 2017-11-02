<?php

namespace AppBundle\Security;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class JwtConfiguration
{
    public static $TTL = 3600; // 1h ttl
    private $builder;
    private $ssh_private;
    private $ssh_passPhrase;
    private $signer;

    public function __construct(String $ssh_private, String $ssh_passPhrase)
    {
        $this->ssh_passPhrase = $ssh_passPhrase;
        $this->ssh_private = $ssh_private;
        $this->signer = new Sha256();
        $this->builder = new Builder();
    }

    public function getSigner(): Sha256
    {
        return $this->signer;
    }

    public function sign(): Builder
    {
        $this->builder->sign($this->signer, $this->getPrivateKey());
        return $this->builder;
    }

    public function getPrivateKey(): Key
    {
        return new Key('file://' . $this->ssh_private, $this->ssh_passPhrase);
    }

    public function getBuilder(): Builder
    {
        return $this->builder;
    }

}
<?php
namespace WondPHP;

use Error;
use  Illuminate\Support\Str as Str;

use WondPHP\Contracts\Encrypter as  EncrypterContracts;
// use Illuminate\Log\LogManager;
use Illuminate\Encryption\Encrypter as Encrypters;
 
class Encrypter implements EncrypterContracts
{
    protected  $Encrypter = null;
    protected static $_instance = [];

     
    public  function __construct()
    {
        $this->registerEncrypter();
        $this->registerOpisSecurityKey();
    }

  
 
    /**
     * Register the encrypter.
     *
     * @return void
     */
    protected function registerEncrypter()
    {
            $config = config('app');

            $this->Encrypter = new Encrypters($this->parseKey($config), $config['cipher']);
    }

    /**
     * Configure Opis Closure signing for security.
     *
     * @return void
     */
    protected function registerOpisSecurityKey()
    {
        $config = config('app');

        if (! class_exists(SerializableClosure::class) || empty($config['key'])) {
            return;
        }

        SerializableClosure::setSecretKey($this->parseKey($config));
    }

    /**
     * Parse the encryption key.
     *
     * @param  array  $config
     * @return string
     */
    protected function parseKey(array $config)
    {
        if (Str::startsWith($key = $this->key($config), $prefix = 'base64:')) {
            $key = base64_decode(Str::after($key, $prefix));
        }

        return $key;
    }

    /**
     * Extract the encryption key from the given configuration.
     *
     * @param  array  $config
     * @return string
     *
     * @throws \Illuminate\Encryption\MissingAppKeyException
     */
    protected function key(array $config)
    {
        return tap($config['key'], function ($key) {
            if (empty($key)) {
                throw new MissingAppKeyException;
            }
        });
    }





    /**
     * Encrypt a string without serialization.
     *
     * @param  string  $value
     * @return string
     *
     * @throws \Illuminate\Contracts\Encryption\EncryptException
     */
    public function encryptString($value)
    {
        return $this->encrypt($value, false);
    }

/**
     * Encrypt the given value.
     *
     * @param  mixed  $value
     * @param  bool  $serialize
     * @return string
     *
     * @throws \Illuminate\Contracts\Encryption\EncryptException
     */
    public function encrypt($value, $serialize = true)
    {

        return $this->Encrypter->encrypt($value,$serialize);

    }


    public function decryptString($payload)
    {
        return $this->decrypt($payload, false);
    }

    /**
     * Decrypt the given value.
     *
     * @param  string  $payload
     * @param  bool  $unserialize
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Encryption\DecryptException
     */
    public function decrypt($payload, $unserialize = true)
    {

        return $this->Encrypter->decrypt($payload,$unserialize);

    }



 
 
    
}

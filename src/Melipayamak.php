<?php

namespace Pishehgostar\ExchangeMelipayamak;

use Illuminate\Support\Facades\Log;

class Melipayamak
{
    const PATH = "https://rest.payamak-panel.com/api/SendSMS/%s";

    protected string $username;

    protected string $password;

    protected string $from;

    public function __construct()
    {
        $this->username = config('services.melipayamak.username', '');
        $this->password = config('services.melipayamak.password', '');
        $this->from = config('services.melipayamak.from', '');
    }

    public function setFrom(string $from)
    {
        $this->from = $from;
    }

    public function getFrom()
    {
        return $this->from;
    }

    protected function getPath($path, $method): string
    {
        return sprintf($path, $method);
    }

    protected function execute($url, $data = null): bool|string
    {

        $fields_string = "";

        if (!is_null($data)) {

            $fields_string = http_build_query($data);

        }

        $handle = curl_init();

        curl_setopt($handle, CURLOPT_URL, $url);

        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($handle, CURLOPT_POST, true);

        curl_setopt($handle, CURLOPT_POSTFIELDS, $fields_string);


        $response = curl_exec($handle);

        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        $curl_errno = curl_errno($handle);

        $curl_error = curl_error($handle);

        if ($curl_errno) {

            throw new \Exception($curl_error);

        }

        curl_close($handle);

        return $response;


    }

    /**
     * @throws \Exception
     */
    public function send($to, $from, $text, $isFlash = false): bool|string
    {

        $url = $this->getPath(self::PATH, 'SendSMS');

        $data = [
            'UserName' => $this->username,
            'PassWord' => $this->password,
            'To' => $to,
            'From' => $from,
            'Text' => $text,
            'IsFlash' => $isFlash
        ];

        return $this->execute($url, $data);

    }

    /**
     * @throws \Exception
     */
    public function sendByBaseNumber($text, $to, $bodyId): bool|string
    {

        $url = $this->getPath(self::PATH, 'BaseServiceNumber');

        $data = [
            'username' => $this->username,
            'password' => $this->password,
            'text' => implode(';',$text),
            'to' => $to,
            'bodyId' => $bodyId
        ];
        Log::info($data);

        return $this->execute($url, $data);

    }

}

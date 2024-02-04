<?php

namespace Pishehgostar\ExchangeMelipayamak;


class MelipayamakSmsMessage
{
    protected Melipayamak $melipayamak;
    public array $payload = [];


    public function __construct()
    {
        $this->melipayamak = app(Melipayamak::class);
        $this->payload['from'] = $this->melipayamak->getFrom();
    }

    public function from(string $from): self
    {
        $this->payload['from'] = $from;
        return $this;
    }

    public function pattern(string $patternNumber, array $parameters): self
    {
        $this->payload['method'] = 'pattern';
        $this->payload['pattern'] = $patternNumber;
        $this->payload['parameters'] = $parameters;
        return $this;
    }

    public function simple(string $text, bool $isFlash = false): self
    {
        $this->payload['method'] = 'simple';
        $this->payload['text'] = $text;
        $this->payload['isFlash'] = $isFlash;
        return $this;
    }

    public function toArray(): array
    {
        return $this->payload;
    }

    public function to(string $number): self
    {
        $this->payload['to'] = $number;

        return $this;
    }

    public function toNotGiven(): bool
    {
        return !isset($this->payload['to']);
    }


    /**
     * @throws \Exception
     */
    public function send():bool|string
    {
        $method = $this->payload['method'] ?? '';
        switch ($method) {
            case 'pattern':
                if (!isset($this->payload['parameters']) || !isset($this->payload['to']) || !isset($this->payload['pattern'])){
                    throw new \Exception('Melipayamak: Invalid data is provided for pattern sms');
                }
                return $this->melipayamak->sendByBaseNumber($this->payload['parameters'],$this->payload['to'],$this->payload['pattern']);
            case 'simple':
                if (!isset($this->payload['to']) || !isset($this->payload['from']) || !isset($this->payload['text']) || !isset($this->payload['isFlash'])) {
                    throw new \Exception('Melipayamak: Invalid data is provided for simple sms');
                }
                return $this->melipayamak->send($this->payload['to'],$this->payload['from'],$this->payload['text'],$this->payload['isFlash']);
            default:
                throw new \Exception('Melipayamak: Invalid method is requested');
        }
    }
}

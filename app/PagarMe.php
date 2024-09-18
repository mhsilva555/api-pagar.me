<?php

namespace Api\PagarMe;

class PagarMe
{
    private $apiKey;
    private $apiBaseUrl = 'https://api.pagar.me/1/';

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    // Método para criar uma transação
    public function createTransaction($amount, $card_hash, $customerData, $metadata = [])
    {
        $data = [
            'api_key' => $this->apiKey,
            'amount' => $amount,
            'card_hash' => $card_hash,
            'customer' => $customerData,
            'metadata' => $metadata
        ];

        return $this->sendRequest('transactions', 'POST', $data);
    }

    // Método para capturar uma transação
    public function captureTransaction($transactionId, $amount = null)
    {
        $endpoint = 'transactions/' . $transactionId . '/capture';
        $data = ['api_key' => $this->apiKey];

        if ($amount) {
            $data['amount'] = $amount;
        }

        return $this->sendRequest($endpoint, 'POST', $data);
    }

    // Método para consultar uma transação
    public function getTransaction($transactionId)
    {
        $endpoint = 'transactions/' . $transactionId;
        $data = ['api_key' => $this->apiKey];

        return $this->sendRequest($endpoint, 'GET', $data);
    }

    // Método para criar um cliente
    public function createCustomer($name, $email, $cpf, $phone)
    {
        $data = [
            'api_key' => $this->apiKey,
            'name' => $name,
            'email' => $email,
            'documents' => [
                [
                    'type' => 'cpf',
                    'number' => $cpf,
                ]
            ],
            'phone_numbers' => [$phone]
        ];

        return $this->sendRequest('customers', 'POST', $data);
    }

    // Método para fazer requisições à API
    private function sendRequest($endpoint, $method, $data = [])
    {
        $url = $this->apiBaseUrl . $endpoint;
        $ch = curl_init();

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        } elseif ($method === 'GET') {
            $url .= '?' . http_build_query($data);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('Erro ao fazer a requisição: ' . curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}
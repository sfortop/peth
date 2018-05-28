<?php
/**
 * Copyright SuntechSoft (c) 2017-2018.
 */

/**
 * Created by Serhii Borodai <serhii.borodai@globalgames.net>
 */

namespace Handler;


use EthereumRPC\API\Eth;
use EthereumRPC\EthereumRPC;
use EthereumRPC\Response\Transaction;
use EthereumRPC\Response\TransactionInputTransfer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

class Test implements RequestHandlerInterface
{
    /**
     * @var EthereumRPC
     */
    private $client;

    /**
     * Test constructor.
     * @param EthereumRPC $client
     */
    public function __construct()
    {
        $client = new EthereumRPC('34.196.204.233', 8545);

        $this->client = $client;

    }


    /**
     * Handle the request and return a response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
    ini_set('max_execution_time',0);
        $eth = new Eth($this->client);

//        $data = $eth->getTransaction('0x2f4de8883d6f79ccbfdfe08c31d7a547243a06859cbeb5bb78048942f8054eab');

        $block = $eth->getBlock(5673908);


        /** @var Transaction $transactionHash */
        foreach ($block->transactions as $transactionHash) {
            $transaction = $eth->getTransaction($transactionHash);
            if ($transaction->to == '0x2ccbff3a042c68716ed2a2cb0c544a9f1d1935e1') {
                /** @var TransactionInputTransfer $input */
                $input = $transaction->input();
                if ($input instanceof TransactionInputTransfer) {

                    $data[$transactionHash]['input'] = [
                        $input->amount,
                        $input->payee
                    ];
                    $data[$transactionHash]['trn'] = $transaction;
                } else {
                    $data['eth'][$transactionHash] = [
                        $transaction->from,
                        $transaction->to,
                        $transaction->input(),
                    ];
                }
            }
        }
        //$this->client->jsonRPC('getBlock', [5673908]);
        //$erc20 = new \ERC20\ERC20($this->client);
        //$dmt = $erc20->token('0x2ccbff3a042c68716ed2a2cb0c544a9f1d1935e1');
        //$this->client->contract()->address('0x2ccbff3a042c68716ed2a2cb0c544a9f1d1935e1');

        return new JsonResponse($data, 200, [], JSON_PRETTY_PRINT);
    }
}


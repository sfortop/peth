
#php ethereum transaction monitoring


all scripts work with Redis list and put result to own list to next processing

* block-announcer - read last announced block and announces next pack of blocks to parse
* block-reader - read transactions from announced blocks 
* transaction-reader - read transactions info from  previous list
* transaction-checker - check if transactions are belong to contract
* transaction-announcer - pass checked transaction to RabbitMQ
 
 RabbitMQ configuration passed by env variables
 
    'host' => getenv('PGTW_RMQ_HOST')?: 'rabbitmq',
    'port' => getenv('PGTW_RMQ_PORT')?: 5672,
    'login' => getenv('PGTW_RMQ_USER') ?:'guest',
    'password' => getenv('PGTW_RMQ_PASS') ?:'guest', 
    
 Redis configuration passed by env variables
 
     'host' => getenv('PGTW_REDIS') ?: 'redis',
     'port' => getenv('PGTW_REDIS_PORT') ?: '6379',
     'db'   => getenv('PGTW_REDIS_DB') ?: '2',   
 `geth` node configuration
 
     'host' => parse_url(getenv('ETHEREUM_NODE_URL'))['host'] ?? null,
 
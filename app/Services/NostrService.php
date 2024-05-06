<?php

namespace App\Services;

use App\Grpc\nostr\JobInput;
use App\Grpc\nostr\JobParam;
use App\Grpc\nostr\PoolConnectorClient;
use App\Grpc\nostr\RpcRequestJob;
use Exception;
use Grpc\ChannelCredentials;

class NostrService
{
    protected $poolAddress;

    protected $query;

    protected $documents = [];

    protected $k = 1;

    protected $max_tokens = 512;

    protected $overlap = 128;

    protected $encryptFor = '';

    protected $warmUp = false;

    protected $cacheDuration = -1;

    protected $quantize = true;

    public function poolAddress($poolAddress)
    {
        $this->poolAddress = $poolAddress;

        return $this;
    }

    public function query($query)
    {
        $this->query = $query;

        return $this;
    }

    public function documents($documents)
    {
        $this->documents = $documents;

        return $this;
    }

    public function k($k)
    {
        $this->k = $k;

        return $this;
    }

    public function maxTokens($maxTokens)
    {
        $this->max_tokens = $maxTokens;

        return $this;
    }

    public function quantize($quantize)
    {
        $this->quantize = $quantize;

        return $this;
    }

    public function overlap($overlap)
    {
        $this->overlap = $overlap;

        return $this;
    }

    public function encryptFor($encryptFor)
    {
        $this->encryptFor = $encryptFor;

        return $this;
    }

    public function warmUp($warmUp)
    {
        $this->warmUp = $warmUp;

        return $this;
    }

    public function cacheDurationhint($cacheDuration)
    {
        $this->cacheDuration = $cacheDuration;

        return $this;
    }

    public function execute()
    {

        $currentime = now();
        $expiresAt = $currentime->addMinutes(10);

        $requestJob = new RpcRequestJob();

        $requestJob->setRunOn('openagents/extism-runtime');
        $requestJob->setExpireAfter($expiresAt->timestamp);

        $inputs = [];
        foreach ($this->documents as $document) {
            $input = new JobInput();
            $input->setData($document)->setType('url')->setMarker('passage');
            $inputs[] = $input;
        }

        $inputq = new JobInput();
        $inputq->setData($this->query)->setType('text')->setMarker('query');
        $inputs[] = $inputq;

        $requestJob->setInput($inputs);

        $param1 = new JobParam();
        $param1->setKey('max-tokens')->setValue(["$this->max_tokens"]);

        $param2 = new JobParam();
        $param2->setKey('overlap')->setValue(["$this->overlap"]);

        $param3 = new JobParam();
        $param3->setKey('quantize')->setValue(["$this->quantize"]);

        $param4 = new JobParam();
        $param4->setKey('k')->setValue(["$this->k"]);

        $param5 = new JobParam();
        $param5->setKey('cache-duration-hint')->setValue(["$this->cacheDuration"]);

        $param6 = new JobParam();
        $param6->setKey('warm-up')->setValue(["$this->warmUp"]);

        $param7 = new JobParam();
        $param7->setKey('main')->setValue(['https://github.com/OpenAgentsInc/openagents-rag-coordinator-plugin/releases/download/v0.2/rag.wasm']);

        $requestJob->setParam([$param1, $param2, $param3, $param4, $param5, $param6, $param7]);

        $requestJob->setDescription('RAG pipeline');
        $requestJob->setKind(5003);
        $requestJob->setOutputFormat('application/json');

        if ($this->encryptFor != null) {
            $requestJob->setEncrypted(true);
            $requestJob->setRequestProvider($this->encryptFor);
        }

        $opts = [
            'credentials' => config('nostr.pool_ssl') ? ChannelCredentials::createSsl() : ChannelCredentials::createInsecure(),
            'update_metadata' => function ($metaData) {
                $metaData['authorization'] = [config('nostr.node_token')];

                return $metaData;
            },
        ];
        $hostname = $this->poolAddress;
        $res = new PoolConnectorClient($hostname, $opts);
        $metadata = [];
        $options = [];
        $jobres = $res->requestJob($requestJob, $metadata, $options);
        $result = $jobres->wait();
        $status = $result[1]->code;

        if ($status !== 0) {
            throw new Exception($result[1]->details);
        }

        return $result[0]->getId();
    }
}

<?php
namespace GoogleSpreadsheet\Tests\Google\Spreadsheet;

use Google\Spreadsheet\ServiceRequestInterface;

class TestServiceRequest implements ServiceRequestInterface
{
    private $retVal;

    public function setExecuteReturn($retVal)
    {
        $this->retVal = $retVal;
    }

    public function execute()
    {
        if($this->retVal instanceof \Exception) {
            throw new $this->retVal;
        }
        
        return $this->retVal;
    }

    public function delete($url)
    {
        return $this->execute();
    }

    public function get($url)
    {
        return $this->execute();
    }

    public function post($url, $postData)
    {
        return $this->execute();
    }

    public function put($url, $postData)
    {
        return $this->execute();
    }

}
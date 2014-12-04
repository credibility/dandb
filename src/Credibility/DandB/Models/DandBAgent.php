<?php

namespace Credibility\DandB\Models;

class DandBAgent extends BaseModel {

    private $agentId;
    private $agentOfficeCode;
    private $assignedAgentCode;



    /**
     * @param mixed $agentId
     * @returns this
     */

    public function setAgentId($agentId) {
        $this->agentId = $agentId;
        return $this;
    }

    /**
     * @param mixed $agentOfficeCode
     * @returns this
     */

    public function setAgentOfficeCode($agentOfficeCode) {
        $this->agentOfficeCode = $agentOfficeCode;
        return $this;
    }

    /**
     * @param mixed $assignedAgentCode
     * @returns this
     */

    public function setAssignedAgentCode($assignedAgentCode) {
        $this->assignedAgentCode = $assignedAgentCode;
        return $this;
    }





    /**
     * @returns mixed
     */

    public function getAgentId() {
        return $this->agentId;
    }

    /**
     * @returns mixed
     */

    public function getAgentOfficeCode() {
        return $this->agentOfficeCode;
    }

    /**
     * @returns mixed
     */

    public function getAssignedAgentCode() {
        return $this->assignedAgentCode;
    }

}
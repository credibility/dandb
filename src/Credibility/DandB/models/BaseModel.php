<?php

namespace Credibility\DandB;


class BaseModel {

    public function toArray() {
        return get_object_vars($this);
    }

}
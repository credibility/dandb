<?php

namespace Credibility\DandB\Models;


class BaseModel {

    public function toArray() {
        return get_object_vars($this);
    }

}
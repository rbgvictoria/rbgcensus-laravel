<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of BaseModel
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class BaseModel extends Model {
    
    /**
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (array_key_exists($key, $this->relations)) {
            return parent::getAttribute($key);
        } else {
            return parent::getAttribute(snake_case($key));
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        return parent::setAttribute(snake_case($key), $value);
    }
    
    /**
     * @return string
     */
    public function getDateFormat()
    {
         return 'Y-m-d H:i:s.u';
    }
}

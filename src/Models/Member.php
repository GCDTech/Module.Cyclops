<?php

namespace Gcd\Cyclops\Models;

use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Schema\Columns\DateTimeColumn;
use Rhubarb\Stem\Schema\Columns\StringColumn;
use Rhubarb\Stem\Schema\ModelSchema;

class Member extends Model
{
    /**
     * Returns the schema for this data object.
     *
     * @return \Rhubarb\Stem\Schema\ModelSchema
     */
    protected function createSchema()
    {
        $schema = new ModelSchema('Member');
        $schema->addColumn(
            new StringColumn('CyclopsID', 50),
            new DateTimeColumn('DateChanged')
        );

        return $schema;
    }
}
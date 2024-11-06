<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Traits;

use MyParcelCom\ApiSdk\Utils\StringUtils;

trait JsonSerializable
{
    /**
     * This function puts all object properties in an array and returns it.
     */
    public function jsonSerialize(): array
    {
        $json = $this->arrayValuesToArray(get_object_vars($this));

        // Remove all empty top-level properties (id, attributes, relationships, meta).
        foreach ($json as $property => $value) {
            if ($this->isEmpty($value)) {
                unset($json[$property]);
            }
        }

        // If there are attributes, make sure no empty properties are serialized.
        if (isset($json['attributes'])) {
            foreach ($json['attributes'] as $attribute => $value) {
                if ($this->isEmpty($value)) {
                    unset($json['attributes'][$attribute]);
                }
            }
        }

        // If there are relationships, remove any possible 'attributes' still present on the related resource.
        // This can happen when a resource (not a proxy) is set as a relationship on another resource.
        if (isset($json['relationships'])) {
            $json['relationships'] = $this->removeRelationshipAttributes($json['relationships']);
        }

        // If there is a meta, make sure no empty properties are serialized
        if (isset($json['meta'])) {
            foreach ($json['meta'] as $attribute => $value) {
                if ($this->isEmpty($value)) {
                    unset($json['meta'][$attribute]);
                }
            }
        }

        return $json;
    }

    private function isEmpty(mixed $values): bool
    {
        if ($values === [] || $values === null) {
            return true;
        }

        if (!is_array($values)) {
            return false;
        }

        $empty = true;
        foreach ($values as $value) {
            $empty = $empty && $this->isEmpty($value);
        }

        return $empty;
    }

    /**
     * Helper function to recursively convert all values in an array to scalar values or arrays with scalar values.
     */
    private function arrayValuesToArray(array $arrayValues): array
    {
        $array = [];
        foreach ($arrayValues as $key => $value) {
            $key = is_string($key) ? StringUtils::camelToSnakeCase($key) : $key;
            $isObjectOrClass = is_object($value) || (is_string($value) && class_exists($value));

            if (is_scalar($value)) {
                $array[$key] = $value;
            } elseif (is_array($value)) {
                $array[$key] = $this->arrayValuesToArray($value);
            } elseif ($isObjectOrClass && method_exists($value, 'jsonSerialize')) {
                $array[$key] = $value->jsonSerialize();
            }
        }

        return $array;
    }

    /**
     * Remove all the attributes from the relationships, so it only has `id` and `type` values.
     */
    private function removeRelationshipAttributes(array $relationships): array
    {
        foreach ($relationships as $name => &$relationship) {
            if (empty($relationship['data'])) {
                unset($relationships[$name]);
                continue;
            }
            if (isset($relationship['data']['id'])) {
                unset($relationship['data']['attributes'], $relationship['data']['relationships']);
                continue;
            }
            foreach ($relationship['data'] as &$relationResource) {
                unset($relationResource['attributes'], $relationResource['relationships']);
            }
        }
        unset($relationship);

        return $relationships;
    }
}

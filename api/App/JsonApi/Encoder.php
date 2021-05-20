<?php

namespace App\JsonApi;

use App\JsonApi;
use App\JsonApi\Document\JsonApiObject;
use App\JsonApi\Document\LinksObject;
use App\JsonApi\Document\PrimaryData\ResourceCollection;
use App\JsonApi\Document\PrimaryData\ResourceObject;
use App\MVC\Model;
use App\MVC\Query\Result;
use App\Utils\JSON;

class Encoder {

    private $includePaths;


    public function withUrlPrefix($urlPrefix) {

    }

    public function withEncodeOptions($options) {

    }

    public function withFieldSets($fieldSet) {

    }

    public function withIncludedPaths($paths) {
        $this->includePaths = $paths;
    }


    public function encode($data) {
        $document = new Document(
            new JsonApiObject("1.0")
        );

        if ($data instanceof Result) {
            $document->setMeta([
                'count' => $data->getFoundRows(),
            ]);

            $document->setLinks(LinksObject::pagination($data->getFoundRows()));

            $resourceCollection = $data->toJsonApi();

            foreach (array_keys($resourceCollection->getResources()) as $key) {
                $model = $data->get($key);
                $resourceObject = $resourceCollection->getResources()[$key];

                if ($model instanceof Model && $resourceObject instanceof ResourceObject) {
                    $this->included($document, $model, $resourceObject);
                }
            }

            $document->setData($resourceCollection);
        }

        elseif ($data instanceof Model) {
            $resourceObject = $data->toJsonApi();

            $this->included($document, $data, $resourceObject);

            $document->setData($resourceObject);
        }

        return JSON::encode($document->toArray());
    }


    public function included(Document &$document, Model &$data, ResourceObject &$dataResource, $include = null) {
        if ($include == null)
            $include = implode(',', JsonApi::getInclude()) ?? "";

        if (empty($include))
            return;

        foreach(explode(',', $include) as $relationshipPath) {
            $relationshipLeft = explode('.', $relationshipPath);

            $relationshipName = array_shift($relationshipLeft);

            $related = $data->getRelated($relationshipName);
            if ($related == null) continue;
            $relatedResource = $related->toJsonApi();

            $dataResource->relationship($relationshipName)->setData($relatedResource);
            $document->addResourceToIncluded($relatedResource);

            if (!empty($relationshipLeft)) {

                if ($related instanceof Model && $relatedResource instanceof ResourceObject) {
                    $model = $related;
                    $resourceObject = $relatedResource;

                    $this->included($document, $model, $resourceObject, implode('.', $relationshipLeft));
                }

                elseif ($related instanceof Result && $relatedResource instanceof ResourceCollection) {
                    $keys = array_keys($relatedResource->getResources());

                    foreach ($keys as $key) {
                        $model = $related->get($key);
                        $resourceObject = $relatedResource->getResources()[$key];

                        if ($model instanceof Model && $resourceObject instanceof ResourceObject) {
                            $model->afterGet();
                            $this->included($document, $model, $resourceObject, implode('.', $relationshipLeft));
                        }
                    }

                }
            }
        }
    }


}
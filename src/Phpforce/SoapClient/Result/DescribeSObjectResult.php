<?php

namespace Phpforce\SoapClient\Result;

use Doctrine\Common\Collections\ArrayCollection;
use Phpforce\SoapClient\Result\DescribeSObjectResult\Field;

class DescribeSObjectResult
{
    protected $activateable;
    protected $childRelationships;
    protected $createable;
    protected $custom;
    protected $customSetting;
    protected $deletable;
    protected $deprecatedAndHidden;
    protected $feedEnabled;
    protected $fields;
    protected $keyPrefix;
    protected $label;
    protected $labelPlural;
    protected $layoutable;
    protected $mergeable;
    protected $name;
    protected $queryable;
    protected $replicateable;
    protected $retrieveable;
    protected $searchable;
    protected $triggerable;
    protected $undeletable;
    protected $updateable;

    /**
     * @return boolean
     */
    public function isActivateable()
    {
        return $this->activateable;
    }

    /**
     * @return ArrayCollection
     */
    public function getChildRelationships()
    {
        if (!$this->childRelationships instanceof ArrayCollection) {
            $this->childRelationships = new ArrayCollection($this->childRelationships);
        }
        return $this->childRelationships;
    }

    /**
     * Get child relationship by name
     *
     * @param string $name  Relationship name
     * @return ChildRelationship
     */
    public function getChildRelationship($name)
    {
        return $this->getChildRelationships()->filter(function($input) use ($name) {
            return $name === $input->getRelationshipName();
        })->first();
    }

    /**
     * @return boolean
     */
    public function isCreateable()
    {
        return $this->createable;
    }

    /**
     * @return boolean
     */
    public function isCustom()
    {
        return $this->custom;
    }

    /**
     * @return boolean
     */
    public function isCustomSetting()
    {
        return $this->customSetting;
    }

    /**
     * @return boolean
     */
    public function isDeletable()
    {
        return $this->deletable;
    }

    /**
     * @return boolean
     */
    public function isDeprecatedAndHidden()
    {
        return $this->deprecatedAndHidden;
    }

    /**
     * @return boolean
     */
    public function isFeedEnabled()
    {
        return $this->feedEnabled;
    }

    /**
     *
     * @return ArrayCollection|Field[]
     */
    public function getFields()
    {
        if (!$this->fields instanceof ArrayCollection) {
            $this->fields = new ArrayCollection($this->fields);
        }
        return $this->fields;
    }

    /**
     * Get field description by field name
     *
     * @param string $field Field name
     * @return Field
     */
    public function getField($field)
    {
        return $this->getFields()->filter(function($input) use ($field) {
            return $field === $input->getName();
        })->first();
    }

    /**
     * @return string
     */
    public function getKeyPrefix()
    {
        return $this->keyPrefix;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getLabelPlural()
    {
        return $this->labelPlural;
    }

    /**
     * @return boolean
     */
    public function isLayoutable()
    {
        return $this->layoutable;
    }

    /**
     * @return boolean
     */
    public function isMergeable()
    {
        return $this->mergeable;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function isQueryable()
    {
        return $this->queryable;
    }

    /**
     * @return boolean
     */
    public function isReplicateable()
    {
        return $this->replicateable;
    }

    /**
     * @return boolean
     */
    public function isRetrieveable()
    {
        return $this->retrieveable;
    }

    /**
     * @return boolean
     */
    public function isSearchable()
    {
        return $this->searchable;
    }

    /**
     * @return boolean
     */
    public function isTriggerable()
    {
        return $this->triggerable;
    }

    /**
     * @return boolean
     */
    public function isUndeletable()
    {
        return $this->undeletable;
    }

    /**
     * @return boolean
     */
    public function isUpdateable()
    {
        return $this->updateable;
    }

    /**
     * Get all fields that constitute relationships to other objects
     *
     * @return ArrayCollection
     */
    public function getRelationshipFields()
    {
        return $this->getFields()->filter(function($field) {
            return null !== $field->getRelationshipName();
        });
    }

    /**
     * Get a relationship field
     *
     * @param string $name
     * @return Field
     */
    public function getRelationshipField($name)
    {
        return $this->getRelationshipFields()->filter(function($field) use ($name) {
            return $name === $field->getName();
        })->first();
    }
}
<?php
class OrganicInternet_SimpleConfigurableProducts_Checkout_Block_Cart_Item_Renderer
    extends Mage_Checkout_Block_Cart_Item_Renderer
{
    protected $_items = array();

    protected function getConfigurableProductParentId()
    {
        if ($this->getItem()->getOptionByCode('cpid')) {
            return $this->getItem()->getOptionByCode('cpid')->getValue();
        }
        return null;
    }

    protected function getConfigurableProductParent()
    {
        return Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($this->getConfigurableProductParentId());
    }

    public function getItemsProduct()
    {
        if(!array_key_exists($this->getItem()->getId(), $this->_items)) {
            $this->_items[$this->getItem()->getId()] =
                Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($this->getItem()->getProductId());
        }
        return $this->_items[$this->getItem()->getId()];
    }

    public function getProductName()
    {
        if ($this->getConfigurableProductParentId()) {
            return $this->getConfigurableProductParent()->getName();
        } else {
            return $this->getItemsProduct()->getSku().$this->getItemsProduct()->getName();
        }
    }

    public function getProductUrl()
    {
        if ($this->getConfigurableProductParentId()) {
            return $this->getConfigurableProductParent()->getProductUrl();
        } else {
            return $this->getItemsProduct()->getProductUrl();
        }
    }

    public function getAttributesValue($attribute)
    {
        return $this->getItemsProduct()->getAttributeText($attribute->getAttributeCode());
    }

    public function getOptionList()
    {
        $options = parent::getOptionList();
        $attributes = $this->getConfigurableProductParent()
            ->getTypeInstance()
            ->getUsedProductAttributes();

        foreach($attributes as $attribute) {
            $options[] = array(
                'label' => $attribute->getFrontendLabel(),
                'value' => $this->getAttributesValue($attribute),
                'option_id' => $attribute->getId(),
            );
        }
        return $options;
    }

    public function getProductThumbnail()
    {
        if ($this->getConfigurableProductParentId()) {
            return $this->helper('catalog/image')->init($this->getItemsProduct(), 'thumbnail');
        } else {
            return $this->helper('catalog/image')->init($this->getConfigurableProductParent(), 'thumbnail');
        }
    }
}
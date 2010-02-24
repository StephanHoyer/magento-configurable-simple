<?php
class OrganicInternet_SimpleConfigurableProducts_Checkout_Block_Cart_Item_Renderer
    extends Mage_Checkout_Block_Cart_Item_Renderer
{
    protected $_product;

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

    public function getProduct()
    {
        if(!isset($this->_product)) {
            $this->_product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($this->getItem()->getProductId());
        }
        return $this->_product;
    }

    public function getProductName()
    {
        if ($this->getConfigurableProductParentId()) {
            return $this->getConfigurableProductParent()->getName();
        } else {
            return $this->getProduct()->getName();
        }
    }

    public function getProductUrl()
    {
        if ($this->getConfigurableProductParentId()) {
            return $this->getConfigurableProductParent()->getProductUrl();
        } else {
            return $this->getProduct()->getProductUrl();
        }
    }

    public function getOptionList()
    {
        $options = parent::getOptions();
        $attributes = $this->getConfigurableProductParent()
            ->getTypeInstance()
            ->getUsedProductAttributes();
        foreach($attributes as $attribute) {
            $options[] = array(
                'label' => $attribute->getFrontendLabel(),
                'value' => $this->getProduct()->getAttributeText($attribute->getAttributeCode()),
                'option_id' => $attribute->getId(),
            );
        }
        return $options;
    }

    public function getProductThumbnail()
    {
        if ($this->getConfigurableProductParentId()) {
            return $this->helper('catalog/image')->init($this->getProduct(), 'thumbnail');
        } else {
            return $this->helper('catalog/image')->init($this->getConfigurableProductParent(), 'thumbnail');
        }
    }
}
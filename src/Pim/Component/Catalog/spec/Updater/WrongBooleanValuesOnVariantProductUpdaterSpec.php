<?php
declare(strict_types=1);

namespace spec\Pim\Component\Catalog\Updater;

use PhpSpec\ObjectBehavior;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\FamilyInterface;
use Pim\Component\Catalog\Model\FamilyVariantInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Model\ValueCollectionInterface;
use Pim\Component\Catalog\Model\ValueInterface;

class WrongBooleanValuesOnVariantProductUpdaterSpec extends ObjectBehavior
{
    function it_updates_wrong_boolean_values_on_impacted_variant_products(
        ProductInterface $productImpacted,
        FamilyInterface $boots,
        AttributeInterface $booleanAttribute,
        FamilyVariantInterface $familyVariant,
        ValueCollectionInterface $valuesForVariation,
        ValueCollectionInterface $values,
        ValueInterface $booleanValue
    ) {
        $productImpacted->isVariant()->willReturn(true);
        $productImpacted->getFamily()->willReturn($boots);
        $boots->getAttributes()->willReturn([$booleanAttribute]);
        $booleanAttribute->getType()->willReturn('pim_catalog_boolean');
        $booleanAttribute->getCode()->willReturn('bool_attribute');

        $productImpacted->getFamilyVariant()->willReturn($familyVariant);
        $familyVariant->getLevelForAttributeCode('bool_attribute')->willReturn(0);
        $familyVariant->getNumberOfLevel()->willReturn(1);

        $productImpacted->getValuesForVariation()->willReturn($valuesForVariation);
        $valuesForVariation->getByCodes('bool_attribute')->willReturn($booleanValue);

        $productImpacted->getValues()->willReturn($values);
        $values->removeByAttribute($booleanAttribute)->shouldBeCalled();
        $productImpacted->setValues($values)->shouldBeCalled();

        $this->updateProduct($productImpacted);
    }

    function it_does_not_update_product_without_boolean_in_their_family(
        ProductInterface $productNotImpacted,
        FamilyInterface $boots,
        AttributeInterface $textAttribute,
        FamilyVariantInterface $familyVariant
    ) {
        $productNotImpacted->isVariant()->willReturn(true);
        $productNotImpacted->getFamily()->willReturn($boots);
        $boots->getAttributes()->willReturn([$textAttribute]);
        $textAttribute->getType()->willReturn('pim_catalog_text');
        $textAttribute->getCode()->willReturn('text_attribute');

        $productNotImpacted->getFamilyVariant()->willReturn($familyVariant);
        $familyVariant->getLevelForAttributeCode('bool_attribute')->willReturn(0);
        $familyVariant->getNumberOfLevel()->willReturn(1);

        $this->updateProduct($productNotImpacted);
    }

    function it_does_not_update_product_if_boolean_is_on_product_level(
        ProductInterface $productNotImpacted,
        FamilyInterface $boots,
        AttributeInterface $booleanAttribute,
        FamilyVariantInterface $familyVariant
    ) {
        $productNotImpacted->isVariant()->willReturn(true);
        $productNotImpacted->getFamily()->willReturn($boots);
        $boots->getAttributes()->willReturn([$booleanAttribute]);
        $booleanAttribute->getType()->willReturn('pim_catalog_boolean');
        $booleanAttribute->getCode()->willReturn('bool_attribute');

        $productNotImpacted->getFamilyVariant()->willReturn($familyVariant);
        $familyVariant->getLevelForAttributeCode('bool_attribute')->willReturn(1);
        $familyVariant->getNumberOfLevel()->willReturn(1);

        $this->updateProduct($productNotImpacted);
    }

    function it_does_not_update_product_if_product_does_not_have_any_value_on_this_attribute(
        ProductInterface $productNotImpacted,
        FamilyInterface $boots,
        AttributeInterface $booleanAttribute,
        FamilyVariantInterface $familyVariant,
        ValueCollectionInterface $valuesForVariation
    ) {
        $productNotImpacted->isVariant()->willReturn(true);
        $productNotImpacted->getFamily()->willReturn($boots);
        $boots->getAttributes()->willReturn([$booleanAttribute]);
        $booleanAttribute->getType()->willReturn('pim_catalog_boolean');
        $booleanAttribute->getCode()->willReturn('bool_attribute');

        $productNotImpacted->getFamilyVariant()->willReturn($familyVariant);
        $familyVariant->getLevelForAttributeCode('bool_attribute')->willReturn(0);
        $familyVariant->getNumberOfLevel()->willReturn(1);

        $productNotImpacted->getValuesForVariation()->willReturn($valuesForVariation);
        $valuesForVariation->getByCodes('bool_attribute')->willReturn(null);

        $this->updateProduct($productNotImpacted);
    }

    function it_does_not_update_product_if_product_is_not_variant(
        ProductInterface $productNotImpacted
    ) {
        $productNotImpacted->isVariant()->willReturn(false);

        $productNotImpacted->getValues()->shouldNotBeCalled();

        $this->updateProduct($productNotImpacted);
    }
}

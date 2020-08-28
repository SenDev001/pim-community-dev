<?php

namespace AkeneoTest\Pim\Enrichment\Integration\Product\Export\Product;

use AkeneoTest\Pim\Enrichment\Integration\Product\Export\AbstractExportTestCase;

/**
 * @group ce
 */
class ExportProductsWithLabelsIntegration extends AbstractExportTestCase
{
    public function testProductExportWithLabels()
    {
        $expectedCsvWithTranslations = <<<CSV
Activé;"Association avec des quantitées Modèles de produit";"Association avec des quantitées Modèles de produit Quantité";"Association avec des quantitées Produits";"Association avec des quantitées Produits Quantité";Catégories;Couleur;"Est-ce en vente ?";Famille;Groupes;"Nom (anglais États-Unis)";"Pack Groupes";"Pack Modèles de produit";"Pack Produits";Parent;"Remplacement Groupes";"Remplacement Modèles de produit";"Remplacement Produits";Taille;"Une métrique";"Une métrique (Unité)";"Vente croisée Groupes";"Vente croisée Modèles de produit";"Vente croisée Produits";"Vente incitative Groupes";"Vente incitative Modèles de produit";"Vente incitative Produits";[sku]
Oui;;;;;T-shirt;Bleu,Rose;Oui;Vêtements;;;;;;"Tshirt Appolon";;;;"Taille M";12;Kilogramme;;;;;;;apollon_pink_m
Oui;"Tshirt Appolon";5;"Tshirt Appolon";12;Été,T-shirt;Bleu,Rose;Non;Vêtements;;;;;;;;;;"Taille L";12;Kilogramme;;"Tshirt Appolon";"Tshirt Appolon";;;;summer_shirt

CSV;
        $this->assertProductExport(
            $expectedCsvWithTranslations,
            ['header_with_label' => true, 'with_label' => true, 'withHeader' => true, 'file_locale' => 'fr_FR']
        );
    }
    public function testProductExportWithMissingLabelsForTheLocale()
    {
        $expectedCsvWithNoTranslations = <<<CSV
[categories];[color];[enabled];[family];[groups];[is_on_sale];[metric];"[metric] ([unit])";"[name] ([en_US])";"[PACK] [groups]";"[PACK] [products]";"[PACK] [product_models]";[parent];"[QUANTITY] [products]";"[QUANTITY] [products] [quantity]";"[QUANTITY] [product_models]";"[QUANTITY] [product_models] [quantity]";[size];[sku];"[SUBSTITUTION] [groups]";"[SUBSTITUTION] [products]";"[SUBSTITUTION] [product_models]";"[UPSELL] [groups]";"[UPSELL] [products]";"[UPSELL] [product_models]";"[X_SELL] [groups]";"[X_SELL] [products]";"[X_SELL] [product_models]"
[tshirt];[blue],[pink];[yes];[clothing];;[yes];12;[KILOGRAM];;;;;[apollon];;;;;[m];apollon_pink_m;;;;;;;;;
[summer],[tshirt];[blue],[pink];[yes];[clothing];;[no];12;[KILOGRAM];;;;;;[apollon_pink_m];12;[apollon];5;[l];summer_shirt;;;;;;;;[apollon_pink_m];[apollon]

CSV;
        $this->assertProductExport(
            $expectedCsvWithNoTranslations,
            ['header_with_label' => true, 'with_label' => true, 'withHeader' => true, 'file_locale' => 'unknown_locale']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function loadFixtures() : void
    {
        // Attributes
        $this->createAttribute([
            'code'        => 'is_on_sale',
            'type'        => 'pim_catalog_boolean',
            'group'       => 'attributeGroupA',
            'localizable' => false,
            'scopable'    => false,
            'labels' => [
                'fr_FR' => 'Est-ce en vente ?'
            ]
        ]);
        $this->createAttribute([
            'code'        => 'color',
            'type'        => 'pim_catalog_multiselect',
            'group'       => 'attributeGroupA',
            'localizable' => false,
            'scopable'    => false,
            'labels' => [
                'fr_FR' => 'Couleur'
            ]
        ]);
        $this->createAttributeOption([
            'code'        => 'pink',
            'attribute'   => 'color',
            'labels' => [
                'fr_FR' => 'Rose'
            ]
        ]);
        $this->createAttributeOption([
            'code'        => 'blue',
            'attribute'   => 'color',
            'labels' => [
                'fr_FR' => 'Bleu'
            ]
        ]);
        $this->createAttribute([
            'code'        => 'size',
            'type'        => 'pim_catalog_simpleselect',
            'group'       => 'attributeGroupA',
            'localizable' => false,
            'scopable'    => false,
            'labels' => [
                'fr_FR' => 'Taille'
            ]
        ]);
        $this->createAttributeOption([
            'code'        => 'm',
            'attribute'   => 'size',
            'labels' => [
                'fr_FR' => 'Taille M'
            ]
        ]);
        $this->createAttributeOption([
            'code'        => 'l',
            'attribute'   => 'size',
            'labels' => [
                'fr_FR' => 'Taille L'
            ]
        ]);
        $this->createAttribute([
            'code'        => 'metric',
            'type'        => 'pim_catalog_metric',
            'group'       => 'attributeGroupA',
            'localizable' => false,
            'scopable'    => false,
            'decimals_allowed' => false,
            'metric_family' => 'Weight',
            'default_metric_unit' => 'KILOGRAM',
            'negative_allowed' => false,
            'labels' => [
                'fr_FR' => 'Une métrique'
            ]
        ]);
        $this->createAttribute([
            'code'        => 'name',
            'type'        => 'pim_catalog_text',
            'group'       => 'attributeGroupA',
            'localizable' => true,
            'scopable'    => false,
            'labels' => [
                'fr_FR' => 'Nom'
            ]
        ]);

        // Families
        $this->createFamily([
            'code'        => 'clothing',
            'attributes'  => ['sku', 'color', 'size', 'is_on_sale', 'metric', 'name'],
            'attribute_as_label' => 'name',
            'labels' => [
                'fr_FR' => 'Vêtements'
            ]
        ]);
        $this->createFamilyVariant([
            'code'        => 'clothing_color_size',
            'family'      => 'clothing',
            'variant_attribute_sets' => [
                [
                    'level' => 1,
                    'axes' => ['size'],
                    'attributes' => ['color', 'size', 'sku'],
                ],
            ],
            'labels' => [
                'fr_FR' => 'Vêtements par couleurs et tailles'
            ]
        ]);

        // Categories
        $this->createCategory(['code' => 'tshirt', 'labels' => ['fr_FR' => 'T-shirt']]);
        $this->createCategory(['code' => 'summer', 'parent' => 'tshirt', 'labels' => ['fr_FR' => 'Été']]);

        // Associations
        $this->createAssociationType(
            [
                'code' => 'QUANTITY',
                'is_quantified'=> true,
                'labels' => [
                    'fr_FR' => 'Association avec des quantitées'
                ]
            ]
        );


        // Products
        $this->createProductModel(
            [
                'code' => 'apollon',
                'family_variant' => 'clothing_color_size',
                'categories' => ['tshirt'],
                'values'  => [
                    'name' => [['data' => 'Tshirt Appolon', 'locale' => 'fr_FR', 'scope' => null]],
                    'is_on_sale' => [['data' => true, 'locale' => null, 'scope' => null]],
                    'metric'  => [['data' => ['amount' => 12, 'unit' => 'KILOGRAM'], 'locale' => null, 'scope' => null]],
                ]
            ]
        );
        $this->createVariantProduct(
            'apollon_pink_m',
            [
                'family' => 'clothing',
                'parent' => 'apollon',
                'categories' => [],
                'values'  => [
                    'size'  => [['data' => 'm', 'locale' => null, 'scope' => null]],
                    'color'  => [['data' => ['pink','blue'], 'locale' => null, 'scope' => null]],
                ]
            ]
        );

        $this->createProduct(
            'summer_shirt',
            [
                'family' => 'clothing',
                'categories' => ['tshirt', 'summer'],
                'values'  => [
                    'name' => [['data' => 'summer_shirt_2020', 'locale' => 'fr_FR', 'scope' => null]],
                    'color' => [['data' => ['pink', 'blue'], 'locale' => null, 'scope' => null]],
                    'size'  => [['data' => 'l', 'locale' => null, 'scope' => null]],
                    'is_on_sale' => [['data' => false, 'locale' => null, 'scope' => null]],
                    'metric'  => [['data' => ['amount' => 12, 'unit' => 'KILOGRAM'], 'locale' => null, 'scope' => null]],
                ],
                'associations' => [
                    'X_SELL' => [
                        'products'=> ['apollon_pink_m'],
                        'product_models'=> ['apollon'],
                    ]
                ],
                'quantified_associations' => [
                    'QUANTITY' => [
                        'products' => [
                            ['identifier'=> 'apollon_pink_m', 'quantity' => 12]
                        ],
                        'product_models' => [
                            ['identifier'=> 'apollon', 'quantity' => 5]
                        ]
                    ]
                ]
            ]
        );
    }
}

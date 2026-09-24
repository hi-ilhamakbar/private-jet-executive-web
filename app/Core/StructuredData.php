<?php

declare(strict_types=1);

namespace App\Core;

final class StructuredData
{
    /** @return array<string, mixed> */
    public static function forPage(string $siteUrl, string $canonicalUrl, string $canonicalPath, string $title, string $description): array
    {
        $organisationId = $siteUrl . '/#organization';
        $websiteId = $siteUrl . '/#website';
        $page = [
            '@type' => 'WebPage',
            '@id' => $canonicalUrl . '#webpage',
            'url' => $canonicalUrl,
            'name' => $title,
            'description' => $description,
            'inLanguage' => 'en',
            'isPartOf' => ['@id' => $websiteId],
            'about' => ['@id' => $organisationId],
        ];

        if ($canonicalPath === '/') {
            $page['primaryImageOfPage'] = [
                '@type' => 'ImageObject',
                'contentUrl' => $siteUrl . '/assets/images/hero-private-jet-sunrise.png',
            ];

            return [
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'Organization',
                        '@id' => $organisationId,
                        'name' => 'Private Jet Executive',
                        'url' => $siteUrl . '/',
                        'logo' => $siteUrl . '/assets/images/logo-transparent.png',
                        'email' => 'charter@privatejetexecutive.com',
                        'address' => [
                            '@type' => 'PostalAddress',
                            'streetAddress' => 'Ruko Jl. Pandanaran No. 1C Kav. 9, Pekunden, Kec. Semarang Tengah',
                            'addressLocality' => 'Semarang',
                            'addressRegion' => 'Jawa Tengah',
                            'postalCode' => '50134',
                            'addressCountry' => 'ID',
                        ],
                        'contactPoint' => [[
                            '@type' => 'ContactPoint',
                            'contactType' => 'customer service',
                            'email' => 'charter@privatejetexecutive.com',
                            'availableLanguage' => ['English', 'Indonesian'],
                        ]],
                    ],
                    [
                        '@type' => 'WebSite',
                        '@id' => $websiteId,
                        'url' => $siteUrl . '/',
                        'name' => 'Private Jet Executive',
                        'alternateName' => 'PrivateJetExecutive.com',
                        'publisher' => ['@id' => $organisationId],
                        'inLanguage' => 'en',
                    ],
                    $page,
                ],
            ];
        }

        $page['breadcrumb'] = [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $siteUrl . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $title, 'item' => $canonicalUrl],
            ],
        ];

        return ['@context' => 'https://schema.org', '@graph' => [$page]];
    }
}

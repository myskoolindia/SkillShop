<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabOffer extends Model
{
    use HasFactory;

    protected $table = 'lab_offers';

    protected $fillable = [
        'title',
        'slug',
        'badge_text',
        'subtitle',
        'banner_image',
        'is_active',
        'booking_enabled',
        'booking_token_amount',
        'booking_extra_discount_percent',
        'booking_title',
        'booking_subtitle',
        'countdown_end',
        'whatsapp_number',
        'support_email',
        'support_phone',
        'highlights',
        'terms',
        'products',
    ];

    protected $casts = [
        'is_active'                      => 'boolean',
        'booking_enabled'                => 'boolean',
        'booking_token_amount'           => 'float',
        'booking_extra_discount_percent' => 'float',
        'countdown_end'                  => 'datetime',
        'highlights'                     => 'array',
        'products'                       => 'array',
    ];

    /**
     * Get or initialize the default campaign offer
     */
    public static function getActiveOffer(): ?self
    {
        $offer = self::first();
        if (!$offer) {
            $offer = self::createDefaultOffer();
        }
        return $offer;
    }

    /**
     * Create default initial offer data
     */
    public static function createDefaultOffer(): self
    {
        return self::create([
            'title'           => 'Special Skill Lab Festival Offer',
            'slug'            => 'lab-offer',
            'badge_text'      => '🔥 EXCLUSIVE SCHOOL PROMOTION — SAVE UP TO 35% ON TURNKEY LABS',
            'subtitle'        => 'Transform your school with state-of-the-art STEM, Robotics, AI, and CBSE Composite Skill Labs at exclusive promotional pricing. Includes complete equipment, curriculum, student kits, and certified teacher training.',
            'banner_image'    => null,
            'is_active'       => true,
            'countdown_end'   => now()->addDays(20)->endOfDay(),
            'whatsapp_number' => '+919900000000',
            'support_email'   => 'admissions@skillvation.com',
            'support_phone'   => '+91 80 4718 1234',
            'highlights'      => [
                '100% CBSE Circular Skill-75/2024 & NEP 2020 Aligned',
                'Turnkey Hardware, Tools & Equipment (600+ items)',
                'Free On-Site Implementation & Safety Workstations',
                'Teacher Masterclass Certification with SOPs',
                'Hands-on Student Project Kits (20+ kits included)',
                '1-Year Comprehensive Warranty & Dedicated Support',
            ],
            'terms'           => 'Special promotional prices are valid exclusively for recognized schools and educational institutions during the campaign period. Prices are inclusive of turnkey installation, curricular manuals, and teacher enablement workshops. GST and shipping are calculated as per government norms.',
            'products'        => [
                [
                    'id'              => 'promo_1',
                    'product_id'      => 74,
                    'title'           => 'Skillvation Lab Basic',
                    'subtitle'        => 'Turnkey STEM, Robotics & Hands-On Practical Maker Hub',
                    'badge'           => 'STARTER LAB SETUP',
                    'mrp'             => 150813,
                    'offer_price'     => 150813,
                    'discount_amount' => 0,
                    'features'        => [
                        '12 Core STEM & Practical Maker Modules',
                        'Hands-On Electronics, Sensors & IoT Tools',
                        '20 Student Practical Experiment Kits',
                        'Grade 1–10/12 NEP 2020 Aligned Curriculum',
                        'Virtual Teacher Mentorship & Project Walkthroughs',
                        'Direct Hardware & Kit Support',
                    ],
                    'package_slug'    => 'skillvation-lab',
                    'cart_url'        => '/club-shop/skillvation-cart',
                    'sort_order'      => 1,
                    'is_active'       => true,
                    'stock_status'    => 'in_stock',
                ],
                [
                    'id'              => 'promo_2',
                    'product_id'      => 385,
                    'title'           => 'Skillvation Advance Lab (AI & Robotics Hub)',
                    'subtitle'        => 'Advanced High-Tech Hub with 3D Printing, Drones, IoT & Edge AI',
                    'badge'           => 'FLAGSHIP INNOVATION — SAVE ₹50,000',
                    'mrp'             => 350000,
                    'offer_price'     => 300000,
                    'discount_amount' => 50000,
                    'features'        => [
                        'Anycubic 3D Printer & Filament Fabrication Bench',
                        'Autonomous Robotics Arena & ESP32 Microcontrollers',
                        'Edge AI Vision & Sensor Prototyping Modules',
                        'National Hackathon & Robotics League Mentorship',
                        'Advanced Student Capstone Project Guides',
                        'Masterclass Certification for Lead Instructors',
                    ],
                    'package_slug'    => 'skillvation-lab-advance',
                    'cart_url'        => '/club-shop/skillvation-advance-cart',
                    'sort_order'      => 2,
                    'is_active'       => true,
                    'stock_status'    => 'in_stock',
                ],
                [
                    'id'              => 'promo_3',
                    'product_id'      => 68,
                    'title'           => 'CBSE Composite Skill Lab Basic',
                    'subtitle'        => 'Turnkey Setup Aligned with CBSE Circular Skill-75/2024 (Basic Tier)',
                    'badge'           => 'CBSE MANDATE COMPLIANT — SAVE ₹46,400',
                    'mrp'             => 296400,
                    'offer_price'     => 250000,
                    'discount_amount' => 46400,
                    'features'        => [
                        'All 3 NCF-SE Forms of Work Fully Covered',
                        '16+ Prescribed Skill Domains & Toolsets',
                        'Agriculture, Hydroponics, Electronics & Carpentry',
                        'Mandatory Safety Busbars & Signage Apparatus',
                        'SOPs & Board-Aligned Project Rubrics',
                        '100% Inspection Readiness Guarantee',
                    ],
                    'package_slug'    => 'cbse-composite-skill-lab-basic-68',
                    'cart_url'        => '/club-shop/basic-cart',
                    'sort_order'      => 3,
                    'is_active'       => true,
                    'stock_status'    => 'limited_slots',
                ],
                [
                    'id'              => 'promo_4',
                    'product_id'      => 72,
                    'title'           => 'CBSE Composite Skill Lab Advanced',
                    'subtitle'        => 'Complete 3 Forms of Work with Advanced Activity Kits & Infrastructure',
                    'badge'           => 'MOST POPULAR — SAVE ₹1,35,315',
                    'mrp'             => 676576,
                    'offer_price'     => 541261,
                    'discount_amount' => 135315,
                    'features'        => [
                        'Full Multi-Workstation Infrastructure & Storage',
                        '18+ Advanced Skill Areas with Specialized Kits',
                        'Deep Practical Electronics, IoT & Coding Resources',
                        'NCF-SE Forms 1, 2 & 3 Exhaustive Kit Inventory',
                        'Dedicated Masterclass SOPs & Faculty Enablement',
                        'Comprehensive 1-Year Warranty & Board Readiness',
                    ],
                    'package_slug'    => 'cbse-composite-skill-lab-advanced-72',
                    'cart_url'        => '/club-shop/advance-cart',
                    'sort_order'      => 4,
                    'is_active'       => true,
                    'stock_status'    => 'limited_slots',
                ],
            ],
        ]);
    }
}


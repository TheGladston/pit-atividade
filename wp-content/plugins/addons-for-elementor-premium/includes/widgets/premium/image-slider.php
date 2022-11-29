<?php

/*
Widget Name: Image Slider
Description: Create a responsive image slider.
Author: LiveMesh
Author URI: https://www.livemeshthemes.com
*/

namespace LivemeshAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Scheme_Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class LAE_Image_Slider_Widget extends Widget_Base {

    public function get_name() {
        return 'lae-image-slider';
    }

    public function get_title() {
        return __('Image Slider', 'livemesh-el-addons');
    }

    public function get_icon() {
        return 'lae-icon-slider6';
    }

    public function get_categories() {
        return array('livemesh-addons');
    }

    public function get_custom_help_url() {
        return 'https://livemeshelementor.com/docs/livemesh-addons/premium-addons/image-slider/';
    }

    public function get_script_depends() {
        return [
            'lae-widgets-premium-scripts',
            'lae-frontend-scripts',
            'jquery-flexslider',
            'jquery-nivo',
            'jquery-slick',
            'responsiveslides'
        ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_image_slider',
            [
                'label' => __('Image Slider', 'livemesh-el-addons'),
            ]
        );


        $this->add_control(

            'class', [
                'type' => Controls_Manager::TEXT,
                "description" => __("Provide a unique CSS class for the slider. (optional).", "livemesh-el-addons"),
                "label" => __("Class", "livemesh-el-addons"),
                'prefix_class' => 'lae-image-slider-',
            ]
        );

        $this->add_control(

            'caption_style', [
                'type' => Controls_Manager::SELECT,
                'label' => __('Caption Style', 'livemesh-el-addons'),
                'default' => 'style1',
                'options' => [
                    'style1' => __('Style 1', 'livemesh-el-addons'),
                    'style2' => __('Style 2', 'livemesh-el-addons'),
                ],
            ]
        );

        $this->add_control(
            'image_slider_heading',
            [
                'label' => __('Image Slides', 'livemesh-el-addons'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'image_slides',
            [
                'type' => Controls_Manager::REPEATER,
                'default' => [],
                'fields' => [


                    [
                        'name' => 'slide_image',
                        'label' => __('Slide Image', 'livemesh-el-addons'),
                        'type' => Controls_Manager::MEDIA,
                        'default' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                        'label_block' => true,
                        'dynamic' => [
                            'active' => true,
                        ],
                    ],

                    [
                        'name' => 'slide_url',
                        'label' => __('URL to link to by image and caption heading. (optional)', 'livemesh-el-addons'),
                        'type' => Controls_Manager::URL,
                        'label_block' => true,
                        'default' => [
                            'url' => '',
                            'is_external' => 'false',
                        ],
                        'placeholder' => __('http://your-link.com', 'livemesh-el-addons'),
                        'dynamic' => [
                            'active' => true,
                        ],
                    ],

                    [
                        'name' => 'heading',
                        'label' => __('Caption Heading', 'livemesh-el-addons'),
                        'default' => __('My slide caption', 'livemesh-el-addons'),
                        'type' => Controls_Manager::TEXT,
                        'dynamic' => [
                            'active' => true,
                        ],
                    ],

                    [
                        'name' => 'subheading',
                        'label' => __('Caption Subheading', 'livemesh-el-addons'),
                        'type' => Controls_Manager::TEXT,
                        'dynamic' => [
                            'active' => true,
                        ],
                    ],


                    [
                        'name' => 'caption_button_heading',
                        'label' => __('Caption Button', 'livemesh-el-addons'),
                        'type' => Controls_Manager::HEADING,
                        'dynamic' => [
                            'active' => true,
                        ],
                    ],

                    [
                        'name' => 'button_text',
                        'label' => __('Button Text', 'livemesh-el-addons'),
                        'type' => Controls_Manager::TEXT,
                        'dynamic' => [
                            'active' => true,
                        ],
                    ],

                    [
                        'name' => 'button_url',
                        'label' => __('Button URL', 'livemesh-el-addons'),
                        'type' => Controls_Manager::URL,
                        'label_block' => true,
                        'default' => [
                            'url' => '',
                            'is_external' => 'true',
                        ],
                        'placeholder' => __('http://your-link.com', 'livemesh-el-addons'),
                        'dynamic' => [
                            'active' => true,
                        ],
                    ],

                    [
                        "name" => "button_color",
                        "type" => Controls_Manager::SELECT,
                        "label" => __("Button Color", "livemesh-el-addons"),
                        "options" => array(
                            "default" => __("Default", "livemesh-el-addons"),
                            "custom" => __("Custom", "livemesh-el-addons"),
                            "black" => __("Black", "livemesh-el-addons"),
                            "blue" => __("Blue", "livemesh-el-addons"),
                            "cyan" => __("Cyan", "livemesh-el-addons"),
                            "green" => __("Green", "livemesh-el-addons"),
                            "orange" => __("Orange", "livemesh-el-addons"),
                            "pink" => __("Pink", "livemesh-el-addons"),
                            "red" => __("Red", "livemesh-el-addons"),
                            "teal" => __("Teal", "livemesh-el-addons"),
                            "trans" => __("Transparent", "livemesh-el-addons"),
                            "semitrans" => __("Semi Transparent", "livemesh-el-addons"),
                        ),
                        'default' => 'default',
                    ],

                    [
                        "name" => "button_size",
                        "type" => Controls_Manager::SELECT,
                        "label" => __("Button Size", "livemesh-el-addons"),
                        "options" => array(
                            "medium" => __("Medium", "livemesh-el-addons"),
                            "large" => __("Large", "livemesh-el-addons"),
                            "small" => __("Small", "livemesh-el-addons"),
                        ),
                        'default' => 'medium',
                    ],

                    [
                        "name" => "rounded",
                        'type' => Controls_Manager::SWITCHER,
                        'label' => __('Rounded Button?', 'livemesh-el-addons'),
                        'label_off' => __('No', 'livemesh-el-addons'),
                        'label_on' => __('Yes', 'livemesh-el-addons'),
                        'return_value' => 'yes',
                        'default' => 'no',
                    ]

                ],
                'title_field' => '{{{ heading }}}',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_settings',
            [
                'label' => __('Slider Options', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_SETTINGS
            ]
        );

        $this->add_control(

            'slider_type', [
                'type' => Controls_Manager::SELECT,
                "label" => __("Slider Type", "livemesh-el-addons"),
                'default' => 'flex',
                "options" => [
                    "flex" => __("Flex Slider", "livemesh-el-addons"),
                    "nivo" => __("Nivo Slider", "livemesh-el-addons"),
                    "slick" => __("Slick Slider", "livemesh-el-addons"),
                    "responsive" => __("Responsive Slider", "livemesh-el-addons"),
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail_size',
                'label' => __('Slide Image Size', 'livemesh-el-addons'),
                'default' => 'full',
                'condition' => [
                    'slider_type' => ['flex', 'slick', 'responsive'],
                ],
            ]
        );

        $this->add_control(
            'slide_animation',
            [
                'label' => __('Slider Animation', 'livemesh-el-addons'),
                'type' => Controls_Manager::SELECT,
                'default' => 'slide',
                'options' => [
                    'slide' => __('Slide', 'livemesh-el-addons'),
                    'fade' => __('Fade', 'livemesh-el-addons'),
                ],
                'condition' => [
                    'slider_type' => ['flex'],
                ],
            ]
        );

        $this->add_control(
            'direction',
            [
                'label' => __('Sliding Direction', 'livemesh-el-addons'),
                "description" => __("Select the sliding direction.", "livemesh-el-addons"),
                'type' => Controls_Manager::SELECT,
                'default' => 'horizontal',
                'options' => [
                    'horizontal' => __('Horizontal', 'livemesh-el-addons'),
                    'vertical' => __('Vertical', 'livemesh-el-addons'),
                ],
                'condition' => [
                    'slider_type' => ['flex', 'slick'],
                ],
            ]
        );

        $this->add_control(
            'control_nav',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('No', 'livemesh-el-addons'),
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'separator' => 'before',
                'default' => 'yes',
                "description" => __("Create navigation for paging control of each slide?", "livemesh-el-addons"),
                "label" => __("Control navigation?", "livemesh-el-addons"),
            ]
        );

        $this->add_control(
            'direction_nav',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('No', 'livemesh-el-addons'),
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'no',
                "description" => __("Create navigation for previous/next navigation?", "livemesh-el-addons"),
                "label" => __("Direction navigation?", "livemesh-el-addons"),
            ]
        );

        $this->add_control(
            'thumbnail_nav',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('No', 'livemesh-el-addons'),
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'no',
                "description" => __("Use thumbnails for Control Nav?", "livemesh-el-addons"),
                "label" => __("Thumbnails Navigation?", "livemesh-el-addons"),
                'condition' => [
                    'slider_type' => ['flex', 'nivo'],
                ],
            ]
        );

        $this->add_control(
            'randomize',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('No', 'livemesh-el-addons'),
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'separator' => 'before',
                'default' => 'no',
                "description" => __("Randomize slide order?", "livemesh-el-addons"),
                "label" => __("Randomize slides?", "livemesh-el-addons"),
                'condition' => [
                    'slider_type' => ['flex', 'responsive'],
                ],
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('No', 'livemesh-el-addons'),
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'yes',
                "description" => __("Pause the slideshow when hovering over slider, then resume when no longer hovering.", "livemesh-el-addons"),
                "label" => __("Pause on hover?", "livemesh-el-addons"),
            ]
        );

        $this->add_control(
            'pause_on_action',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('No', 'livemesh-el-addons'),
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'yes',
                "description" => __("Pause the slideshow when interacting with control elements.", "livemesh-el-addons"),
                "label" => __("Pause on action?", "livemesh-el-addons"),
                'condition' => [
                    'slider_type' => ['flex'],
                ],
            ]
        );

        $this->add_control(
            'loop',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('No', 'livemesh-el-addons'),
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'yes',
                "description" => __("Should the animation loop?", "livemesh-el-addons"),
                "label" => __("Loop", "livemesh-el-addons"),
                'condition' => [
                    'slider_type' => ['flex', 'slick'],
                ],
            ]
        );

        $this->add_control(
            'slideshow',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('No', 'livemesh-el-addons'),
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'separator' => 'before',
                'default' => 'yes',
                "description" => __("Animate slider automatically without user intervention?", "livemesh-el-addons"),
                "label" => __("Slideshow", "livemesh-el-addons"),
            ]
        );

        $this->add_control(
            'slideshow_speed',
            [
                "description" => __("Set the speed of the slideshow cycling, in milliseconds", "livemesh-el-addons"),
                "label" => __("Slideshow speed", "livemesh-el-addons"),
                'type' => Controls_Manager::NUMBER,
                'default' => 5000,
            ]
        );


        $this->add_control(
            'animation_speed',
            [
                "description" => __("Set the speed of animations, in milliseconds.", "livemesh-el-addons"),
                "label" => __("Animation speed", "livemesh-el-addons"),
                'type' => Controls_Manager::NUMBER,
                'default' => 600,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_styling',
            [
                'label' => __('Caption Heading', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'heading_tag',
            [
                'label' => __('Heading HTML Tag', 'livemesh-el-addons'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => __('H1', 'livemesh-el-addons'),
                    'h2' => __('H2', 'livemesh-el-addons'),
                    'h3' => __('H3', 'livemesh-el-addons'),
                    'h4' => __('H4', 'livemesh-el-addons'),
                    'h5' => __('H5', 'livemesh-el-addons'),
                    'h6' => __('H6', 'livemesh-el-addons'),
                    'div' => __('div', 'livemesh-el-addons'),
                ],
                'default' => 'h3',
            ]
        );

        $this->add_control(
            'heading_color',
            [
                'label' => __('Heading Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .lae-image-slider .lae-slide .lae-caption .lae-heading, {{WRAPPER}} .lae-image-slider .lae-slide .lae-caption .lae-heading a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'heading_hover_color',
            [
                'label' => __('Heading Hover Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .lae-image-slider .lae-slide .lae-caption .lae-heading a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'heading_hover_border_color',
            [
                'label' => __('Heading Hover Border Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .lae-image-slider .lae-slide .lae-caption .lae-heading a:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'label' => __('Typography', 'livemesh-el-addons'),
                'selector' => '{{WRAPPER}} .lae-image-slider .lae-slide .lae-caption .lae-heading',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_subheading',
            [
                'label' => __('Caption Subheading', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'subheading_color',
            [
                'label' => __('Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-image-slider .lae-slide .lae-caption .lae-subheading' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'subheading_typography',
                'selector' => '{{WRAPPER}} .lae-image-slider .lae-slide .lae-caption .lae-subheading',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_button_styling',
            [
                'label' => __('Caption Button', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'button_padding',
            [
                'label' => __('Button Padding', 'livemesh-el-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .lae-image-slider .lae-slide .lae-caption .lae-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'isLinked' => false,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => __('Typography', 'livemesh-el-addons'),
                'selector' => '{{WRAPPER}} .lae-image-slider .lae-slide .lae-caption .lae-button',
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();

        $dir = is_rtl() ? ' dir="rtl"' : '';

        $style = is_rtl() ? ' style="direction:rtl"' : '';

        $settings = apply_filters('lae_image_slider_' . $this->get_id() . '_settings', $settings);

        $thumbnail_attr = $button_type = '';

        $slider_options = [
            'slide_animation' => $settings['slide_animation'],
            'direction' => $settings['direction'],
            'slideshow_speed' => absint($settings['slideshow_speed']),
            'animation_speed' => absint($settings['animation_speed']),
            'randomize' => ('yes' === $settings['randomize']),
            'loop' => ('yes' === $settings['loop']),
            'slideshow' => ('yes' === $settings['slideshow']),
            'control_nav' => ('yes' === $settings['control_nav']),
            'direction_nav' => ('yes' === $settings['direction_nav']),
            'thumbnail_nav' => ('yes' === $settings['thumbnail_nav']),
            'pause_on_hover' => ('yes' === $settings['pause_on_hover']),
            'pause_on_action' => ('yes' === $settings['pause_on_action'])
        ];

        $output = '<div' . $dir . ' class="lae-image-slider lae-container lae-caption-' . $settings['caption_style']
            . '" data-slider-type="' . $settings['slider_type']
            . '" data-settings=\'' . wp_json_encode($slider_options) . '\'>';

        if ($settings['slider_type'] == 'flex'):

            $slider_id = null;

            if ('yes' == $settings['thumbnail_nav']):

                $carousel_id = uniqid('lae-carousel-');

                $slider_id = uniqid('lae-slider-');

            endif;

            $slider_output = '<div' . $style . (!empty($slider_id) ? ' id="' . $slider_id . '"' : '')
                . (!empty($carousel_id) ? 'data-carousel="' . $carousel_id . '"' : '')
                . ' class="lae-flexslider">';

            $slider_output .= '<ul class="lae-slides">';

            foreach ($settings['image_slides'] as $slide):

                if (!empty($slide['slide_image']) && wp_attachment_is_image($slide['slide_image']['id'])) :

                    if ('yes' == $settings['thumbnail_nav']):

                        $thumbnail_src = wp_get_attachment_image_src($slide['slide_image']['id'], 'medium');

                        if ($thumbnail_src)
                            $thumbnail_attr = 'data-thumb="' . $thumbnail_src[0] . '"';

                    endif;

                    $slide_output = '<li ' . $thumbnail_attr . ' class="lae-slide">';

                    $image_html = lae_get_image_html($slide['slide_image'], 'thumbnail_size', $settings);

                    if (!empty($slide['slide_url']) && !empty($slide['slide_url']['url'])) :

                        $image_html = '<a href="' . esc_url($slide['slide_url']['url'])
                            . '" title="' . esc_html($slide['heading'])
                            . '">' . $image_html . '</a>';
                    endif;

                    $slide_output .= apply_filters('lae_image_slider_flexslider_thumbnail_html', $image_html, $slide, $settings);

                    if (!empty($slide['heading'])):

                        $slider_caption = '<div class="lae-caption">';

                        $slider_subheading = empty($slide['subheading']) ? '' : '<div class="lae-subheading">' . htmlspecialchars_decode($slide['subheading']) . '</div>';

                        $slider_caption .= apply_filters('lae_image_slider_flexslider_slider_subheading_output', $slider_subheading, $slide, $settings);

                        if (!empty($slide['heading'])):

                            if (!empty($slide['slide_url']) && !empty($slide['slide_url']['url'])) :

                                $slider_heading = '<' . esc_html($settings['heading_tag']) . ' class="lae-heading">';

                                $slider_heading .= '<a href="' . esc_url($slide['slide_url']['url'])
                                    . '" title="' . esc_attr($slide['heading'])
                                    . '">' . esc_html($slide['heading']) . '</a>';

                                $slider_heading .= '</' . esc_html($settings['heading_tag']) . '>';

                            else :

                                $slider_heading = '<' . esc_html($settings['heading_tag']) . ' class="lae-heading">' . esc_html($slide['heading']) . '</' . esc_html($settings['heading_tag']) . '>';

                            endif;

                            $slider_caption .= apply_filters('lae_image_slider_flexslider_slider_heading_output', $slider_heading, $slide, $settings);

                        endif;

                        if ($settings['caption_style'] == 'style1' && (!empty($slide['button_url']))) :

                            $color_class = ' lae-' . esc_attr($slide['button_color']);

                            if (!empty($slide['button_type']))
                                $button_type = ' lae-' . esc_attr($slide['button_type']);

                            $rounded = ($slide['rounded'] == 'yes') ? ' lae-rounded' : '';

                            $slider_button = '<a class="lae-button ' . $color_class . $button_type . $rounded
                                . '" href="' . esc_url($slide['button_url']['url'])
                                . '"' . (($slide['button_url']['is_external']) ? ' target="_blank"' : '')
                                . '>' . esc_html($slide['button_text']) . '</a>';

                            $slider_caption .= apply_filters('lae_image_slider_flexslider_slider_button_output', $slider_button, $slide, $settings);

                        endif;

                        $slider_caption .= '</div>';

                        $slide_output .= apply_filters('lae_image_slider_flexslider_slider_caption_output', $slider_caption, $slide, $settings);

                    endif;

                    $slide_output .= '</li>';

                    $slider_output .= apply_filters('lae_image_slider_flexslider_slide_output', $slide_output, $slide, $settings);

                endif;

            endforeach;

            $slider_output .= '</ul><!-- .lae-slides -->';

            $slider_output .= '</div><!-- .lae-flexslider -->';

            $output .= apply_filters('lae_image_slider_flexslider_output', $slider_output, $slider_id, $settings);

            if (!empty($carousel_id)):

                $thumbnail_slider = '<div' . $style . ' id="' . $carousel_id . '" class="lae-thumbnailslider lae-flexslider">';

                $thumbnail_slider .= '<ul class="lae-slides">';

                foreach ($settings['image_slides'] as $slide):

                    if (!empty($slide['slide_image']) && wp_attachment_is_image($slide['slide_image']['id'])) :

                        $thumbnail_slide = '<li class="lae-slide">';

                        $thumbnail_slide .= wp_get_attachment_image($slide['slide_image']['id'], 'medium', false, array('class' => 'lae-image medium', 'alt' => $slide['heading']));

                        $thumbnail_slide .= '</li>';

                        $thumbnail_slider .= apply_filters('lae_image_slider_flexslider_thumbnail_carousel_slide_output', $thumbnail_slide, $slide, $settings);

                    endif;

                endforeach;

                $thumbnail_slider .= '</ul>';

                $thumbnail_slider .= '</div>';

                $output .= apply_filters('lae_image_slider_flexslider_thumbnail_carousel_output', $thumbnail_slider, $carousel_id, $settings);

            endif;

        elseif ($settings['slider_type'] == 'nivo') :

            $nivo_captions = array();

            $slider_output = '<div class="nivoSlider">';

            foreach ($settings['image_slides'] as $slide):

                $slide_output = '';

                $caption_index = uniqid('lae-nivo-caption-');

                if (!empty($slide['slide_image']) && wp_attachment_is_image($slide['slide_image']['id'])) :

                    $thumbnail_src = wp_get_attachment_image_src($slide['slide_image']['id'], 'medium');

                    if ($thumbnail_src)
                        $thumbnail_src = $thumbnail_src[0];

                    if (!empty($slide['slide_url']) && !empty($slide['slide_url']['url'])) :

                        $thumbnail_html = '<a href="' . esc_url($slide['slide_url']['url'])
                            . '" title="' . esc_attr($slide['heading']) . '">';

                        $thumbnail_html .= wp_get_attachment_image($slide['slide_image']['id'], 'full', false, array('class' => 'lae-image full', 'data-thumb' => $thumbnail_src, 'alt' => $slide['heading'], 'title' => ('#' . $caption_index)));

                        $thumbnail_html .= '</a>';

                    else :

                        $thumbnail_html = wp_get_attachment_image($slide['slide_image']['id'], 'full', false, array('class' => 'lae-image full', 'data-thumb' => $thumbnail_src, 'alt' => $slide['heading'], 'title' => ('#' . $caption_index)));

                    endif;

                    $slide_output = apply_filters('lae_image_slider_nivoslider_thumbnail_html', $thumbnail_html, $slide, $settings);

                    if (!empty($slide['heading'])):

                        if (!empty($slide['slide_url']) && !empty($slide['slide_url']['url'])) :

                            $nivo_caption = '<div id="' . $caption_index
                                . '" class="nivo-html-caption">'
                                . '<div class="lae-subheading">' . htmlspecialchars_decode($slide['subheading'])
                                . '</div>' . '<h3 class="lae-heading">'
                                . '<a href="' . esc_url($slide['slide_url']['url'])
                                . '" title="' . esc_attr($slide['heading'])
                                . '">' . esc_html($slide['heading']) . '</a></h3>'
                                . '</div>';

                        else :

                            $nivo_caption = '<div id="' . $caption_index . '" class="nivo-html-caption">' . '<div class="lae-subheading">' . htmlspecialchars_decode($slide['subheading']) . '</div>' . '<h3 class="lae-heading">' . esc_html($slide['heading']) . '</h3>' . '</div>';

                        endif;

                        $nivo_captions[] = apply_filters('lae_image_slider_nivoslider_caption', $nivo_caption, $slide, $settings);

                    endif;

                endif;

                $slider_output .= apply_filters('lae_image_slider_nivoslider_slide_output', $slide_output, $slide, $settings);

            endforeach;

            $slider_output .= '</div>';

            $output .= apply_filters('lae_image_slider_nivoslider_output', $slider_output, $settings);

            $caption_html = '<div class="lae-caption nivo-html-caption">';

            foreach ($nivo_captions as $nivo_caption):

                $caption_html .= $nivo_caption . "\n";

            endforeach;

            $caption_html .= '</div>';

            $output .= apply_filters('lae_image_slider_nivoslider_captions_output', $caption_html, $settings);

        elseif ($settings['slider_type'] == 'slick') :

            $slider_output = '<div class="lae-slickslider">';

            foreach ($settings['image_slides'] as $slide):

                $slide_output = '<div class="lae-slide">';

                if (!empty($slide['slide_image']) && wp_attachment_is_image($slide['slide_image']['id'])) :

                    $image_html = lae_get_image_html($slide['slide_image'], 'thumbnail_size', $settings);

                    if (!empty($slide['slide_url']) && !empty($slide['slide_url']['url'])) :

                        $image_html = '<a href="' . esc_url($slide['slide_url']['url'])
                            . '" title="' . esc_html($slide['heading'])
                            . '">' . $image_html . '</a>';

                    endif;

                    $slide_output .= apply_filters('lae_image_slider_slickslider_thumbnail_html', $image_html, $slide, $settings);

                    $slider_caption = '<div class="lae-caption">';

                    $slider_subheading = empty($slide['subheading']) ? '' : '<div class="lae-subheading">' . htmlspecialchars_decode($slide['subheading']) . '</div>';

                    $slider_caption .= apply_filters('lae_image_slider_slickslider_slider_subheading_output', $slider_subheading, $slide, $settings);

                    if (!empty($slide['heading'])):

                        if (!empty($slide['slide_url']) && !empty($slide['slide_url']['url'])) :

                            $slider_heading = '<h3 class="lae-heading">';

                            $slider_heading .= '<a href="' . esc_url($slide['slide_url']['url'])
                                . '" title="' . esc_attr($slide['heading']) . '">'
                                . esc_html($slide['heading']) . '</a>';

                            $slider_heading .= '</h3>';

                        else :

                            $slider_heading = '<h3 class="lae-heading">' . esc_html($slide['heading']) . '</h3>';

                        endif;

                        $slider_caption .= apply_filters('lae_image_slider_slickslider_slider_heading_output', $slider_heading, $slide, $settings);

                    endif;

                    if ($settings['caption_style'] == 'style1' && (!empty($slide['button_url']))) :

                        $color_class = ' lae-' . esc_attr($slide['button_color']);

                        if (!empty($slide['button_type']))
                            $button_type = ' lae-' . esc_attr($slide['button_type']);

                        $rounded = ($slide['rounded'] == 'yes') ? ' lae-rounded' : '';

                        $slider_button = '<a class="lae-button ' . $color_class . $button_type . $rounded
                            . '" href="' . esc_url($slide['button_url']['url']) . '"'
                            . (($slide['button_url']['is_external']) ? ' target="_blank"' : '')
                            . '>' . esc_html($slide['button_text']) . '</a>';

                        $slider_caption .= apply_filters('lae_image_slider_slickslider_slider_button_output', $slider_button, $slide, $settings);

                    endif;

                    $slider_caption .= '</div><!-- .lae-caption -->';

                    $slide_output .= apply_filters('lae_image_slider_slickslider_slider_caption_output', $slider_caption, $slide, $settings);

                endif;

                $slide_output .= '</div>';

                $slider_output .= apply_filters('lae_image_slider_slickslider_slide_output', $slide_output, $slide, $settings);

            endforeach;

            $slider_output .= '</div>';

            $output .= apply_filters('lae_image_slider_slickslider_output', $slider_output, $settings);

        elseif ($settings['slider_type'] == 'responsive') :

            $slider_output = '<div class="rslides_container">';

            $slider_output .= '<ul class="rslides lae-slide">';

            foreach ($settings['image_slides'] as $slide):

                $slide_output = '<li>';

                if (!empty($slide['slide_image']) && wp_attachment_is_image($slide['slide_image']['id'])) :

                    $image_html = lae_get_image_html($slide['slide_image'], 'thumbnail_size', $settings);

                    if (!empty($slide['slide_url']) && !empty($slide['slide_url']['url'])) :

                        $image_html = '<a href="' . esc_url($slide['slide_url']['url'])
                            . '" title="' . esc_html($slide['heading'])
                            . '">' . $image_html . '</a>';

                    endif;

                    $slide_output .= apply_filters('lae_image_slider_responsiveslider_thumbnail_html', $image_html, $slide, $settings);

                    $slider_caption = '<div class="lae-caption">';

                    $slider_subheading = empty($slide['subheading']) ? '' : '<div class="lae-subheading">' . htmlspecialchars_decode($slide['subheading']) . '</div>';

                    $slider_caption .= apply_filters('lae_image_slider_slickslider_slider_subheading_output', $slider_subheading, $slide, $settings);

                    if (!empty($slide['heading'])):

                        if (!empty($slide['slide_url']) && !empty($slide['slide_url']['url'])) :

                            $slider_heading = '<h3 class="lae-heading">';

                            $slider_heading .= '<a href="' . esc_url($slide['slide_url']['url'])
                                . '" title="' . esc_attr($slide['heading'])
                                . '">' . esc_html($slide['heading']) . '</a>';

                            $slider_heading .= '</h3>';

                        else :

                            $slider_heading = '<h3 class="lae-heading">' . esc_html($slide['heading']) . '</h3>';

                        endif;

                        $slider_caption .= apply_filters('lae_image_slider_responsiveslider_slider_heading_output', $slider_heading, $slide, $settings);

                    endif;

                    if ($settings['caption_style'] == 'style1' && (!empty($slide['button_url']))) :

                        $color_class = ' lae-' . esc_attr($slide['button_color']);

                        if (!empty($slide['button_type']))
                            $button_type = ' lae-' . esc_attr($slide['button_type']);

                        $rounded = ($slide['rounded'] == 'yes') ? ' lae-rounded' : '';


                        $slider_button = '<a class="lae-button ' . $color_class . $button_type . $rounded
                            . '" href="' . esc_url($slide['button_url']['url']) . '" '
                            . (($slide['button_url']['is_external']) ? 'target="_blank"' : '')
                            . '>' . esc_html($slide['button_text']) . '</a>';

                        $slider_caption .= apply_filters('lae_image_slider_responsiveslider_slider_button_output', $slider_button, $slide, $settings);

                    endif;

                    $slider_caption .= '</div>';

                    $slide_output .= apply_filters('lae_image_slider_responsiveslider_slider_caption_output', $slider_caption, $slide, $settings);

                endif;

                $slide_output .= '</li>';

                $slider_output .= apply_filters('lae_image_slider_responsiveslider_slide_output', $slide_output, $slide, $settings);

            endforeach;

            $slider_output .= '</ul>';

            $slider_output .= '</div>';

            $output .= apply_filters('lae_image_slider_responsiveslider_output', $slider_output, $settings);

        endif;

        $output .= '</div>';

        echo apply_filters('lae_image_slider_output', $output, $settings);

    }

    protected function content_template() {
    }

}
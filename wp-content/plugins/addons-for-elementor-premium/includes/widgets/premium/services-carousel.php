<?php

/*
Widget Name: Services Carousel
Description: Capture services in a multi-column, touch friendly carousel.
Author: LiveMesh
Author URI: https://www.livemeshthemes.com
*/

namespace LivemeshAddons\Widgets;


use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\utils;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Scheme_Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class LAE_Services_Carousel_Widget extends Widget_Base {

    public function get_name() {
        return 'lae-services-carousel';
    }

    public function get_title() {
        return __('Services Carousel', 'livemesh-el-addons');
    }

    public function get_icon() {
        return 'lae-icon-slider7';
    }

    public function get_categories() {
        return array('livemesh-addons');
    }

    public function get_custom_help_url() {
        return 'https://livemeshelementor.com/docs/livemesh-addons/';
    }

    public function get_script_depends() {
        return [
            'lae-widgets-premium-scripts',
            'lae-frontend-scripts',
            'jquery-slick'
        ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_services',
            [
                'label' => __('Services', 'livemesh-el-addons'),
            ]
        );

        $this->add_control(

            'style',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __('Choose Style', 'livemesh-el-addons'),
                'default' => 'style1',
                'options' => [
                    'style1' => __('Style 1', 'livemesh-el-addons'),
                    'style2' => __('Style 2', 'livemesh-el-addons'),
                ],
                'prefix_class' => 'lae-services-carousel-',
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail_size',
                'label' => __( 'Icon Image Size', 'livemesh-el-addons' ),
                'description' => __( 'Size of icon image chosen for display', 'livemesh-el-addons' ),
                'default' => 'large',
            ]
        );

        $this->add_control(
            'services',
            [
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'service_title' => __('Web Design', 'livemesh-el-addons'),
                        'service_subtitle' => __('Web Design', 'livemesh-el-addons'),
                        'button_text' => __('Read More', 'livemesh-el-addons'),
                        'button_url' => __('http://your-link.com', 'livemesh-el-addons'),
                        'service_excerpt' => 'Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Donec venenatis vulputate lorem. In hac habitasse aliquam.',
                    ],
                    [
                        'service_title' => __('SEO Services', 'livemesh-el-addons'),
                        'service_subtitle' => __('Web Design', 'livemesh-el-addons'),
                        'button_text' => __('Read More', 'livemesh-el-addons'),
                        'button_url' => __('http://your-link.com', 'livemesh-el-addons'),
                        'service_excerpt' => 'Suspendisse nisl elit, rhoncus eget, elementum ac, condimentum eget, diam. Phasellus nec sem in justo pellentesque facilisis platea dictumst.',
                    ],
                    [
                        'service_title' => __('Brand Marketing', 'livemesh-el-addons'),
                        'service_subtitle' => __('Web Design', 'livemesh-el-addons'),
                        'button_text' => __('Read More', 'livemesh-el-addons'),
                        'button_url' => __('http://your-link.com', 'livemesh-el-addons'),
                        'service_excerpt' => 'Nunc egestas, augue at pellentesque laoreet, felis eros vehicula leo, at malesuada velit leo quis pede. Etiam ut purus mattis mauris sodales.',
                    ],
                    [
                        'service_title' => __('Brand Marketing', 'livemesh-el-addons'),
                        'service_subtitle' => __('Web Design', 'livemesh-el-addons'),
                        'button_text' => __('Read More', 'livemesh-el-addons'),
                        'button_url' => __('http://your-link.com', 'livemesh-el-addons'),
                        'service_excerpt' => 'Nunc egestas, augue at pellentesque laoreet, felis eros vehicula leo, at malesuada velit leo quis pede. Etiam ut purus mattis mauris sodales.',
                    ],
                ],
                'fields' => [

                    [
                        'name' => 'service_image',
                        'label' => __('Service Image', 'livemesh-el-addons'),
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
                        'name' => 'service_title',
                        'label' => __('Service Title', 'livemesh-el-addons'),
                        'type' => Controls_Manager::TEXT,
                        'label_block' => true,
                        'default' => __('My service title', 'livemesh-el-addons'),
                        'dynamic' => [
                            'active' => true,
                        ],
                    ],
                    [
                        'name' => 'service_subtitle',
                        'label' => __('Service Subtitle', 'livemesh-el-addons'),
                        'type' => Controls_Manager::TEXT,
                        'label_block' => true,
                        'default' => __('My service subtitle', 'livemesh-el-addons'),
                        'dynamic' => [
                            'active' => true,
                        ],
                    ],

                    [
                        'name' => 'service_link',
                        'label' => __('Service URL', 'livemesh-el-addons'),
                        'description' => __('The link for the page describing the service.', 'livemesh-el-addons'),
                        'type' => Controls_Manager::URL,
                        'label_block' => true,
                        'default' => [
                            'url' => '',
                            'is_external' => 'true',
                        ],
                        'placeholder' => __('http://service-link.com', 'livemesh-el-addons'),
                        'dynamic' => [
                            'active' => true,
                        ],
                    ],
                    [
                        'name' => 'service_excerpt',
                        'label' => __('Service description', 'livemesh-el-addons'),
                        'type' => Controls_Manager::TEXTAREA,
                        'default' => __('Service description goes here', 'livemesh-el-addons'),
                        'label_block' => true,
                        'dynamic' => [
                            'active' => true,
                        ],
                    ],
                    [
                        'name' => 'button_text',
                        'type' => Controls_Manager::TEXT,
                        'label' => __('Text for Service Link/Button', 'livemesh-el-addons'),
                        'dynamic' => [
                            'active' => true,
                        ],
                    ],

                    [
                        'name' => 'button_url',
                        'label' => __('URL for the Service link/button', 'livemesh-el-addons'),
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

                ],
                'title_field' => '{{{ service_title }}}',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_settings',
            [
                'label' => __('Carousel Settings', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_SETTINGS,
            ]
        );

        $this->add_control(
            'arrows',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('No', 'livemesh-el-addons'),
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'yes',
                'label' => __('Prev/Next Arrows?', 'livemesh-el-addons'),
            ]
        );


        $this->add_control(
            'dots',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('No', 'livemesh-el-addons'),
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'separator' => 'before',
                'default' => 'no',
                'label' => __('Show dot indicators for navigation?', 'livemesh-el-addons'),
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
                'label' => __('Pause on Hover?', 'livemesh-el-addons'),
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('No', 'livemesh-el-addons'),
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'separator' => 'before',
                'default' => 'no',
                'label' => __('Autoplay?', 'livemesh-el-addons'),
                'description' => __('Should the carousel autoplay as in a slideshow.', 'livemesh-el-addons'),
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => __('Autoplay speed in ms', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3000,
            ]
        );


        $this->add_control(
            'animation_speed',
            [
                'label' => __('Autoplay animation speed in ms', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'default' => 300,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_responsive',
            [
                'label' => __('Responsive Options', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_SETTINGS,
            ]
        );

        $this->add_control(
            'heading_desktop',
            [
                'label' => __( 'Desktop', 'livemesh-el-addons' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'after',
            ]
        );



        $this->add_control(
            'gutter',
            [
                'label' => __('Gutter', 'livemesh-el-addons'),
                'description' => __('Space between columns.', 'livemesh-el-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => 0,
                    'right' => 10,
                    'bottom' => 0,
                    'left' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .lae-services-carousel .lae-services-carousel-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_control(
            'display_columns',
            [
                'label' => __('Columns per row', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'default' => 3,
            ]
        );


        $this->add_control(
            'scroll_columns',
            [
                'label' => __('Columns to scroll', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'default' => 3,
            ]
        );

        $this->add_control(
            'heading_tablet',
            [
                'label' => __( 'Tablet', 'livemesh-el-addons' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'tablet_gutter',
            [
                'label' => __('Gutter', 'livemesh-el-addons'),
                'description' => __('Space between columns.', 'livemesh-el-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => 0,
                    'right' => 10,
                    'bottom' => 0,
                    'left' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '(tablet-){{WRAPPER}} .lae-services-carousel .lae-services-carousel-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );



        $this->add_control(
            'tablet_display_columns',
            [
                'label' => __('Columns per row', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'default' => 2,
            ]
        );

        $this->add_control(
            'tablet_scroll_columns',
            [
                'label' => __('Columns to scroll', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'default' => 2,
            ]
        );

        $this->add_control(
            'tablet_width',
            [
                'label' => __('Tablet Resolution', 'livemesh-el-addons'),
                'description' => __('The resolution to treat as a tablet resolution.', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'default' => 800,
            ]
        );


        $this->add_control(
            'heading_mobile',
            [
                'label' => __( 'Mobile Phone', 'livemesh-el-addons' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'mobile_gutter',
            [
                'label' => __('Gutter', 'livemesh-el-addons'),
                'description' => __('Space between columns.', 'livemesh-el-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => 0,
                    'right' => 5,
                    'bottom' => 0,
                    'left' => 5,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '(mobile-){{WRAPPER}} .lae-services-carousel .lae-services-carousel-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'mobile_display_columns',
            [
                'label' => __('Columns per row', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 3,
                'step' => 1,
                'default' => 1,
            ]
        );

        $this->add_control(
            'mobile_scroll_columns',
            [
                'label' => __('Columns to scroll', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 3,
                'step' => 1,
                'default' => 1,
            ]
        );

        $this->add_control(
            'mobile_width',
            [
                'label' => __('Mobile Resolution', 'livemesh-el-addons'),
                'description' => __('The resolution to treat as a mobile resolution.', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'default' => 480,
            ]
        );


        $this->end_controls_section();


        $this->start_controls_section(
            'section_service_title',
            [
                'label' => __('Service Title', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_tag',
            [
                'label' => __( 'Title HTML Tag', 'livemesh-el-addons' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => __( 'H1', 'livemesh-el-addons' ),
                    'h2' => __( 'H2', 'livemesh-el-addons' ),
                    'h3' => __( 'H3', 'livemesh-el-addons' ),
                    'h4' => __( 'H4', 'livemesh-el-addons' ),
                    'h5' => __( 'H5', 'livemesh-el-addons' ),
                    'h6' => __( 'H6', 'livemesh-el-addons' ),
                    'div' => __( 'div', 'livemesh-el-addons' ),
                ],
                'default' => 'h3',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __( 'Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-services-carousel .lae-service .lae-service-text .lae-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label' => __('Hover Color for Link', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-services-carousel .lae-service .lae-service-text .lae-title-link:hover .lae-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .lae-services-carousel .lae-service .lae-service-text .lae-title',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_service_subtitle',
            [
                'label' => __('Service Subtitle', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'subtitle_color',
            [
                'label' => __( 'Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-services-carousel .lae-service .lae-service-text .lae-subtitle' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'subtitle_typography',
                'selector' => '{{WRAPPER}} .lae-services-carousel .lae-service .lae-service-text .lae-subtitle',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_service_text',
            [
                'label' => __('Service Text', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __( 'Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-services-carousel .lae-service .lae-service-text .lae-service-excerpt' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .lae-services-carousel .lae-service .lae-service-text .lae-service-excerpt',
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();

        $dir = is_rtl() ? ' dir="rtl"' : '';

        $settings = apply_filters('lae_services_carousel_' . $this->get_id() . '_settings', $settings);

        $services = $settings['services'];

        $carousel_settings = [
            'arrows' => ('yes' === $settings['arrows']),
            'dots' => ('yes' === $settings['dots']),
            'autoplay' => ('yes' === $settings['autoplay']),
            'autoplay_speed' => absint($settings['autoplay_speed']),
            'animation_speed' => absint($settings['animation_speed']),
            'pause_on_hover' => ('yes' === $settings['pause_on_hover']),
        ];

        $responsive_settings = [
            'display_columns' => $settings['display_columns'],
            'scroll_columns' => $settings['scroll_columns'],
            'gutter' => $settings['gutter'],
            'tablet_width' => $settings['tablet_width'],
            'tablet_display_columns' => $settings['tablet_display_columns'],
            'tablet_scroll_columns' => $settings['tablet_scroll_columns'],
            'tablet_gutter' => $settings['tablet_gutter'],
            'mobile_width' => $settings['mobile_width'],
            'mobile_display_columns' => $settings['mobile_display_columns'],
            'mobile_scroll_columns' => $settings['mobile_scroll_columns'],
            'mobile_gutter' => $settings['mobile_gutter'],

        ];

        $carousel_settings = array_merge($carousel_settings, $responsive_settings);

        if (!empty($services)) :

            $output = '<div' . $dir . ' id="lae-services-carousel-' . uniqid()
                . '" class="lae-services-carousel lae-container" data-settings=\'' . wp_json_encode($carousel_settings) . '\'>';

            foreach ($services as $index => $service):

                $has_link = false;

                if (!empty($service['service_link']['url'])) {

                    $has_link = true;

                    $link_key = 'link_' . $index;

                    $url = $service['service_link'];

                    $this->add_render_attribute($link_key, 'title', $service['service_title']);

                    $this->add_render_attribute($link_key, 'href', $url['url']);

                    if (!empty($url['is_external'])) {
                        $this->add_render_attribute($link_key, 'target', '_blank');
                    }

                    if (!empty($url['nofollow'])) {
                        $this->add_render_attribute($link_key, 'rel', 'nofollow');
                    }
                }

                $child_output = '<div class="lae-services-carousel-item">';

                $child_output .= '<div class="lae-service">';

                if (!empty($service['service_image'])):

                    $child_output .= '<div class="lae-image-wrapper">';

                    $image_html = lae_get_image_html($service['service_image'], 'thumbnail_size', $settings);

                    if ($has_link)
                        $image_html = '<a class="lae-image-link" ' . $this->get_render_attribute_string($link_key) . '>' . $image_html . '</a>';

                    $child_output .= $image_html;

                    $child_output .= '</div>';

                endif;

                $child_output .= '<div class="lae-service-text">';

                $child_output .= '<div class="lae-subtitle">' . esc_html($service['service_subtitle']) . '</div>';

                $title_html = '<' . $settings['title_tag'] . ' class="lae-title">' . esc_html($service['service_title']) . '</' . $settings['title_tag'] . '>';

                if ($has_link)
                    $title_html = '<a class="lae-title-link" ' . $this->get_render_attribute_string($link_key) . '>' . $title_html . '</a>';

                $child_output .= $title_html;

                $child_output .= '<div class="lae-service-excerpt">' . do_shortcode(wp_kses_post($service['service_excerpt'])) . '</div>';

                $child_output .= '<a class="lae-read-more" href="' . esc_url($service['button_url']['url']) . '" ' . (($service['button_url']['is_external']) ? 'target="_blank"' : '') . '>' . $service['button_text'] . '</a>';

                $child_output .= '</div><!-- .lae-service-text -->';

                $child_output .= '</div><!-- .lae-service -->';

                $child_output .= '</div><!--.lae-services-carousel-item -->';

                $output .= apply_filters('lae_service_carousel_item_output', $child_output, $service, $settings);

            endforeach;

            $output .= '</div><!-- .lae-services-carousel -->';

            echo apply_filters('lae_services_carousel_output', $output, $settings);

        endif;


    }

    protected function content_template() {
    }

}
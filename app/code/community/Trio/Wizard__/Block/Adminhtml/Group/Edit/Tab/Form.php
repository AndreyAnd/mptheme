<?php
/**
 * @category	Trio
 * @package		Wizard
 */

class Trio_Wizard_Block_Adminhtml_Group_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {

		$_model = Mage::registry('group_data');
		$form = new Varien_Data_Form();

		$this->setForm($form);

		$fieldset = $form->addFieldset('general_form', array('legend'=>Mage::helper('wizard')->__('General Information')));

		$title = $fieldset->addField('title', 'text', array(
			'name'		=> 'title',
			'label'		=> Mage::helper('wizard')->__('Title'),
			'required'	=> true,
			'class'		=> 'required-entry',
			'value'		=> $_model->getTitle()
		));

		$code = $fieldset->addField('code', 'text', array(
			'name'		=> 'code',
			'label'		=> Mage::helper('wizard')->__('Code'),
			'note'		=> Mage::helper('wizard')->__('a unique identifier that is used to inject the slide group via XML'),
			'required'	=> true,
			'class'		=> 'required-entry validate-code',
			'value'		=> $_model->getCode()
		));

		$position = $fieldset->addField('position', 'select', array(
			'name'		=> 'position',
			'label'		=> Mage::helper('wizard')->__('Position'),
			'required'	=> true,
			'values'	=> Mage::getSingleton('wizard/config_source_position')->toOptionArray(),
			'value'		=> $_model->getPosition()
		));

		$sort_order = $fieldset->addField('sort_order', 'text', array(
			'name'		=> 'sort_order',
			'label'		=> Mage::helper('wizard')->__('Sort Order'),
			'note'		=> Mage::helper('wizard')->__('set the sort order in case of multiple sliders on one page'),
			'required'	=> false,
			'value'		=> $_model->getSortOrder()
		));

		$is_active = $fieldset->addField('is_active', 'select', array(
			'name'		=> 'is_active',
			'label'		=> Mage::helper('wizard')->__('Is Enabled'),
			'required'	=> true,
			'values'	=> Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
			'value'		=> $_model->getIsActive()
		));

		if (!Mage::app()->isSingleStoreMode()) {
			$stores = $fieldset->addField('stores', 'multiselect', array(
				'name'		=> 'stores[]',
				'label'		=> Mage::helper('wizard')->__('Visible In'),
				'required'	=> true,
				'values'	=> Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
				'value'		=> $_model->getStoreId()
			));
		}
		else {
			$stores = $fieldset->addField('stores', 'hidden', array(
				'name'		=> 'stores[]',
				'value'		=> Mage::app()->getStore(true)->getId()
			));
		}

		$fieldset = $form->addFieldset('group_style', array('legend'=>Mage::helper('wizard')->__('Slider Style')));

		$width = $fieldset->addField('width', 'text', array(
			'name'		=> 'width',
			'label'		=> Mage::helper('wizard')->__('Maximum Width Slider'),
			'required'	=> false,
			'note'		=> Mage::helper('wizard')->__('maximum width of the slider in pixels, leave empty or 0 for full responsive width'),
			'value'		=> $_model->getWidth()
		));

		$theme = $fieldset->addField('theme', 'select', array(
			'name'		=> 'theme',
			'label'		=> Mage::helper('wizard')->__('Slider Theme'),
			'required'	=> true,
			'values'	=> Mage::getModel('wizard/config_source_theme')->toOptionArray(),
			'value'		=> $_model->getTheme()
		));

		$type = $fieldset->addField('type', 'select', array(
			'name'		=> 'type',
			'label'		=> Mage::helper('wizard')->__('Slider Type'),
			'required'	=> true,
			'values'	=> Mage::getModel('wizard/config_source_type')->toOptionArray(),
			'value'		=> $_model->getType()
		));

		$thumbnail_size = $fieldset->addField('thumbnail_size', 'text', array(
			'name'		=> 'thumbnail_size',
			'label'		=> Mage::helper('wizard')->__('Thumbnail Width'),
			'note'		=> Mage::helper('wizard')->__('width of the images in carousel, should not be larger then thumbnail upload width in general settings (default is 200)'),
			'required'	=> false,
			'class'		=> 'validate-greater-than-zero',
			'value'		=> $this->returnThumbnailSize()
		));

		$nav_show = $fieldset->addField('nav_show', 'select', array(
			'name'		=> 'nav_show',
			'label'		=> Mage::helper('wizard')->__('Show Navigation Arrows'),
			'required'	=> true,
			'values'	=> Mage::getModel('wizard/config_source_navshow')->toOptionArray(),
			'value'		=> $_model->getNavShow()
		));

		$nav_style = $fieldset->addField('nav_style', 'select', array(
			'name'		=> 'nav_style',
			'label'		=> Mage::helper('wizard')->__('Navigation Arrows Style'),
			'required'	=> true,
			'values'	=> Mage::getModel('wizard/config_source_navstyle')->toOptionArray(),
			'value'		=> $_model->getNavStyle()
		));

		$nav_position = $fieldset->addField('nav_position', 'select', array(
			'name'		=> 'nav_position',
			'label'		=> Mage::helper('wizard')->__('Navigation Arrows Position'),
			'required'	=> true,
			'values'	=> Mage::getModel('wizard/config_source_navposition')->toOptionArray(),
			'value'		=> $_model->getNavPosition()
		));

		$nav_color = $fieldset->addField('nav_color', 'text', array(
			'name'		=> 'nav_color',
			'label'		=> Mage::helper('wizard')->__('Navigation Arrows Color'),
			'class'		=> 'colorpicker',
			'value'		=> $this->returnNavColor(),
			'after_element_html'	=> '<script type="text/javascript">
								solide(".colorpicker").width("248px").modcoder_excolor({
									hue_slider : 7,
									sb_slider : 3,
									border_color : "#849ba3",
									sb_border_color : "#ffffff",
									round_corners : false,
									shadow : false,
									background_color : "#e7efef",
									backlight : false,
									effect : "fade",
									callback_on_ok : function() {}
								});
							</script>
							<style>.modcoder_excolor_clrbox{ height: 16px !important; }</style>'
		));

		$pagination_show = $fieldset->addField('pagination_show', 'select', array(
			'name'		=> 'pagination_show',
			'label'		=> Mage::helper('wizard')->__('Show Pagination'),
			'required'	=> true,
			'values'	=> Mage::getModel('wizard/config_source_paginationshow')->toOptionArray(),
			'value'		=> $_model->getPaginationShow()
		));

		$pagination_style = $fieldset->addField('pagination_style', 'select', array(
			'name'		=> 'pagination_style',
			'label'		=> Mage::helper('wizard')->__('Pagination Style'),
			'required'	=> true,
			'values'	=> Mage::getModel('wizard/config_source_pagination')->toOptionArray(),
			'value'		=> $_model->getPaginationStyle()
		));

		$pagination_position = $fieldset->addField('pagination_position', 'select', array(
			'name'		=> 'pagination_position',
			'label'		=> Mage::helper('wizard')->__('Pagination Position'),
			'required'	=> true,
			'values'	=> Mage::getModel('wizard/config_source_paginationposition')->toOptionArray(),
			'value'		=> $_model->getPaginationPosition()
		));

		$fieldset = $form->addFieldset('group_effects', array('legend'=>Mage::helper('wizard')->__('Slider Effects')));

		$slider_auto = $fieldset->addField('slider_auto', 'select', array(
			'name'		=> 'slider_auto',
			'label'		=> Mage::helper('wizard')->__('Auto Start Animation'),
			'required'	=> true,
			'values'	=> Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
			'value'		=> $_model->getSliderAuto()
		));

		$slider_pauseonaction = $fieldset->addField('slider_pauseonaction', 'select', array(
			'name'		=> 'slider_pauseonaction',
			'label'		=> Mage::helper('wizard')->__('Pause Slider On Navigation'),
			'required'	=> true,
			'values'	=> Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
			'value'		=> $_model->getSliderPauseonaction()
		));

		$slider_pauseonhover = $fieldset->addField('slider_pauseonhover', 'select', array(
			'name'		=> 'slider_pauseonhover',
			'label'		=> Mage::helper('wizard')->__('Pause Slider On Hover'),
			'required'	=> true,
			'values'	=> Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
			'value'		=> $_model->getSliderPauseonhover()
		));

		$slider_animation = $fieldset->addField('slider_animation', 'select', array(
			'name'		=> 'slider_animation',
			'label'		=> Mage::helper('wizard')->__('Animation Type'),
			'required'	=> true,
			'values'	=> Mage::getModel('wizard/config_source_animation')->toOptionArray(),
			'value'		=> $_model->getSliderAnimation()
		));

		$slider_aniduration = $fieldset->addField('slider_aniduration', 'text', array(
			'name'		=> 'slider_aniduration',
			'label'		=> Mage::helper('wizard')->__('Animation Duration'),
			'note'		=> Mage::helper('wizard')->__('in milliseconds (default is 600)'),
			'required'	=> true,
			'class'		=> 'required-entry validate-greater-than-zero',
			'value'		=> $this->returnSliderAniduration()
		));

		$fieldset->addField('slider_direction', 'select', array(
			'name'		=> 'slider_direction',
			'label'		=> Mage::helper('wizard')->__('Animation Direction'),
			'required'	=> true,
			'values'	=> Mage::getModel('wizard/config_source_direction')->toOptionArray(),
			'value'		=> $_model->getSliderDirection()
		));

		$slider_slideduration = $fieldset->addField('slider_slideduration', 'text', array(
			'name'		=> 'slider_slideduration',
			'label'		=> Mage::helper('wizard')->__('Slide Duration'),
			'note'		=> Mage::helper('wizard')->__('in milliseconds (default is 7000)'),
			'required'	=> true,
			'class'		=> 'required-entry validate-greater-than-zero',
			'value'		=> $this->returnSliderSlideduration()
		));

		$slider_random = $fieldset->addField('slider_random', 'select', array(
			'name'		=> 'slider_random',
			'label'		=> Mage::helper('wizard')->__('Random Order'),
			'required'	=> true,
			'values'	=> Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
			'value'		=> $_model->getSliderRandom()
		));

		$slider_smoothheight = $fieldset->addField('slider_smoothheight', 'select', array(
			'name'		=> 'slider_smoothheight',
			'label'		=> Mage::helper('wizard')->__('Smooth Height'),
			'note'		=> Mage::helper('wizard')->__('allow slider to scale height if slider images differ in height'),
			'required'	=> true,
			'values'	=> Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
			'value'		=> $_model->getSliderSmoothheight()
		));

		if( Mage::getSingleton('adminhtml/session')->getGroupData() ) {
			$form->setValues(Mage::getSingleton('adminhtml/session')->getGroupData());
			Mage::getSingleton('adminhtml/session')->setGroupData(null);
		}

		$this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($type->getHtmlId(), $type->getName())
            ->addFieldMap($thumbnail_size->getHtmlId(), $thumbnail_size->getName())
			->addFieldMap($nav_show->getHtmlId(), $nav_show->getName())
			->addFieldMap($nav_style->getHtmlId(), $nav_style->getName())
			->addFieldMap($nav_position->getHtmlId(), $nav_position->getName())
			->addFieldMap($nav_color->getHtmlId(), $nav_color->getName())
			->addFieldMap($pagination_show->getHtmlId(), $pagination_show->getName())
			->addFieldMap($pagination_style->getHtmlId(), $pagination_style->getName())
			->addFieldMap($pagination_position->getHtmlId(), $pagination_position->getName())
			->addFieldMap($slider_auto->getHtmlId(), $slider_auto->getName())
			->addFieldMap($slider_pauseonaction->getHtmlId(), $slider_pauseonaction->getName())
			->addFieldMap($slider_pauseonhover->getHtmlId(), $slider_pauseonhover->getName())
            ->addFieldDependence(
                $thumbnail_size->getName(),
                $type->getName(),
                array('carousel','basic-carousel')
            )
			->addFieldDependence(
                $nav_style->getName(),
                $nav_show->getName(),
                array('always','hover')
            )
			->addFieldDependence(
                $nav_position->getName(),
                $nav_show->getName(),
                array('always','hover')
            )
			->addFieldDependence(
                $nav_color->getName(),
                $nav_show->getName(),
                array('always','hover')
            )
			->addFieldDependence(
                $pagination_style->getName(),
                $pagination_show->getName(),
                array('always','hover')
            )
			->addFieldDependence(
                $pagination_position->getName(),
                $pagination_show->getName(),
                array('always','hover')
            )
			->addFieldDependence(
                $slider_pauseonaction->getName(),
                $slider_auto->getName(),
                1
            )
			->addFieldDependence(
                $slider_pauseonhover->getName(),
                $slider_auto->getName(),
                1
            )
        );

		return parent::_prepareForm();
	}
	
	public function returnSliderAniduration() {
		$_model = Mage::registry('group_data');
		if($_model->getSliderAniduration()) { return $_model->getSliderAniduration(); } else { return '600'; }
	}

	public function returnSliderSlideduration() {
		$_model = Mage::registry('group_data');
		if($_model->getSliderSlideduration()) { return $_model->getSliderSlideduration(); } else { return '7000'; }
	}

	public function returnThumbnailSize() {
		$_model = Mage::registry('group_data');
		if($_model->getThumbnailSize()) { return $_model->getThumbnailSize(); } else { return '200'; }
	}

	public function returnNavColor() {
		$_model = Mage::registry('group_data');
		if($_model->getNavColor()) { return $_model->getNavColor(); } else { return '#666666'; }
	}

}
<?php
/*
** Zabbix
** Copyright (C) 2001-2017 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/


// Create form.
$expression_form = (new CForm())
	->setName('expression')
	->addVar('action', 'popup_trexpr')
	->addVar('dstfrm', $data['dstfrm'])
	->addVar('dstfld1', $data['dstfld1'])
	->addVar('hostid', $data['hostid'])
	->addVar('groupid', $data['groupid'])
	->addVar('itemid', $data['itemid']);

if ($data['parent_discoveryid'] !== '') {
	$expression_form->addVar('parent_discoveryid', $data['parent_discoveryid']);
}

// Create form list.
$expression_form_list = new CFormList();

// Append item to form list.
$action = '?action=popup&writeonly=1&dstfrm='.$expression_form->getName();
$action .= ($data['groupid'] && $data['hostid'])
				? '&groupid='.$data['groupid'].'&hostid='.$data['hostid']
				: '';
$action .= '&dstfld1=itemid&dstfld2=description';
$action .= ($data['parent_discoveryid'] !== '') ? '&normal_only=1' : '';
$action .= '&srctbl=items&srcfld1=itemid&srcfld2=name';

$item = [
	(new CTextBox('description', $data['description'], true))->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH),
	(new CDiv())->addClass(ZBX_STYLE_FORM_INPUT_MARGIN),
	(new CButton('select', _('Select')))
		->addClass(ZBX_STYLE_BTN_GREY)
		->onClick(('javascript: PopUp("'.$action.'");'))
];

if ($data['parent_discoveryid'] !== '') {
	$action = '?action=popup&dstfrm='.$expression_form->getName().
		'&dstfld1=itemid&dstfld2=description'.url_param('parent_discoveryid', true).
		'&srctbl=item_prototypes&srcfld1=itemid&srcfld2=name';

	$item[] = (new CDiv())->addClass(ZBX_STYLE_FORM_INPUT_MARGIN);
	$item[] = (new CButton('select', _('Select prototype')))
		->addClass(ZBX_STYLE_BTN_GREY)
		->onClick('javascript: PopUp("'.$action.'");');
}

$expression_form_list->addRow(_('Item'), $item);

$function_combo_box = new CComboBox('expr_type', $data['expr_type'], 'reloadPopup(this.form, "popup_trexpr")');
foreach ($data['functions'] as $id => $f) {
	$function_combo_box->addItem($id, $f['description']);
}
$expression_form_list->addRow(_('Function'), $function_combo_box);

if (array_key_exists('params', $data['functions'][$data['selectedFunction']])) {
	foreach ($data['functions'][$data['selectedFunction']]['params'] as $paramid => $param_function) {
		$param_value = array_key_exists($paramid, $data['params']) ? $data['params'][$paramid] : null;

		if ($param_function['T'] == T_ZBX_INT) {
			$param_type_element = null;

			if ($paramid == 0
				|| ($paramid == 1
					&& (substr($data['expr_type'], 0, 6) === 'regexp'
						|| substr($data['expr_type'], 0, 7) === 'iregexp'
						|| (substr($data['expr_type'], 0, 3) === 'str' && substr($data['expr_type'], 0, 6) !== 'strlen')))) {
				if (array_key_exists('M', $param_function)) {
					$param_type_element = new CComboBox('paramtype', $data['paramtype'], null, $param_function['M']);
				}
				else {
					$expression_form->addVar('paramtype', PARAM_TYPE_TIME);
					$param_type_element = _('Time');
				}
			}

			if ($paramid == 1
					&& (substr($data['expr_type'], 0, 3) !== 'str' || substr($data['expr_type'], 0, 6) === 'strlen')
					&& substr($data['expr_type'], 0, 6) !== 'regexp'
					&& substr($data['expr_type'], 0, 7) !== 'iregexp') {
				$param_type_element = _('Time');
				$param_field = (new CTextBox('params['.$paramid.']', $param_value))->setWidth(ZBX_TEXTAREA_SMALL_WIDTH);
			}
			else {
				$param_field = ($data['paramtype'] == PARAM_TYPE_COUNTS)
					? (new CNumericBox('params['.$paramid.']', (int) $param_value, 10))
						->setWidth(ZBX_TEXTAREA_NUMERIC_STANDARD_WIDTH)
					: (new CTextBox('params['.$paramid.']', $param_value))->setWidth(ZBX_TEXTAREA_SMALL_WIDTH);
			}

			$expression_form_list->addRow($param_function['C'], [
				$param_field,
				(new CDiv())->addClass(ZBX_STYLE_FORM_INPUT_MARGIN),
				$param_type_element
			]);
		}
		else {
			$expression_form_list->addRow($param_function['C'],
				(new CTextBox('params['.$paramid.']', $param_value))->setWidth(ZBX_TEXTAREA_SMALL_WIDTH)
			);
			$expression_form->addVar('paramtype', PARAM_TYPE_TIME);
		}
	}
}
else {
	$expression_form->addVar('paramtype', PARAM_TYPE_TIME);
}

$expression_form_list->addRow('N', (new CTextBox('value', $data['value']))->setWidth(ZBX_TEXTAREA_SMALL_WIDTH));

$expression_form->addItem(
	(new CTabView())->addTab('expressionTab', _('Trigger expression condition'), $expression_form_list)
);

$output = [
	'header' => $data['title'],
	'body' => (new CDiv([$data['errors'], $expression_form]))->toString(),
	'buttons' => [
		[
			'title' => _('Insert'),
			'class' => '',
			'keepOpen' => true,
			'action' => 'return validate_trigger_expression("expression", '.
					'jQuery(window.document.forms["expression"]).closest("[data-dialogueid]").attr("data-dialogueid"));'
		]
	],
	'script_inline' =>
		'jQuery(document).ready(function() {'.
			'\'use strict\';'.
			''.
			'jQuery("#paramtype").change(function() {'.
				'if (jQuery("#expr_type option:selected").val().substr(0, 4) === "last"'.
						'|| jQuery("#expr_type option:selected").val().substr(0, 6) === "strlen"'.
						'|| jQuery("#expr_type option:selected").val().substr(0, 4) === "band") {'.
					'if (jQuery("#paramtype option:selected").val() == '.PARAM_TYPE_COUNTS.') {'.
						'jQuery("#params_0").removeAttr("readonly");'.
					'}'.
					'else {'.
						'jQuery("#params_0").attr("readonly", "readonly");'.
					'}'.
				'}'.
			'});'.

			'if (jQuery("#expr_type option:selected").val().substr(0, 4) === "last"'.
					'|| jQuery("#expr_type option:selected").val().substr(0, 6) === "strlen"'.
					'|| jQuery("#expr_type option:selected").val().substr(0, 4) === "band") {'.
				'if (jQuery("#paramtype option:selected").val() == '.PARAM_TYPE_COUNTS.') {'.
					'jQuery("#params_0").removeAttr("readonly");'.
				'}'.
				'else {'.
					'jQuery("#params_0").attr("readonly", "readonly");'.
				'}'.
			'}'.
		'});'
];

echo (new CJson())->encode($output);

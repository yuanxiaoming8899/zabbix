<?php
/*
** Zabbix
** Copyright (C) 2001-2019 Zabbix SIA
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


class CRouter {
	/**
	 * Layout used for view rendering.
	 *
	 * @var string
	 */
	private $layout = null;

	/**
	 * Controller class for action handling.
	 *
	 * @var string
	 */
	private $controller = null;

	/**
	 * View used to generate HTML, CSV, JSON and other content.
	 *
	 * @var string
	 */
	private $view = null;

	/**
	 * Unique action (request) identifier.
	 *
	 * @var string
	 */
	private $action = null;

	/**
	 * Mapping between action and corresponding controller, layout and view.
	 *
	 * @var array
	 */
	private $routes = [
		// action						controller											layout					view
		'acknowledge.create'			=> ['CControllerAcknowledgeCreate',					null,					null],
		'acknowledge.edit'				=> ['CControllerAcknowledgeEdit',					'layout.htmlpage',		'monitoring.acknowledge.edit'],
		'authentication.edit'			=> ['CControllerAuthenticationEdit',				'layout.htmlpage',		'administration.authentication.edit'],
		'authentication.update'			=> ['CControllerAuthenticationUpdate',				null,					null],
		'autoreg.edit'					=> ['CControllerAutoregEdit',						'layout.htmlpage',		'administration.autoreg.edit'],
		'autoreg.update'				=> ['CControllerAutoregUpdate',						null,					null],
		'dashboard.delete'				=> ['CControllerDashboardDelete',					null,					null],
		'dashboard.list'				=> ['CControllerDashboardList',						'layout.htmlpage',		'monitoring.dashboard.list'],
		'dashboard.properties.check'	=> ['CControllerDashboardPropertiesCheck',			'layout.json',			null],
		'dashboard.properties.edit'		=> ['CControllerDashboardPropertiesEdit',			'layout.json',			'dashboard.properties.edit'],
		'dashboard.share.edit'			=> ['CControllerDashboardShareEdit',				'layout.json',			'dashboard.sharing.edit'],
		'dashboard.share.update'		=> ['CControllerDashboardShareUpdate',				'layout.json',			null],
		'dashboard.update'				=> ['CControllerDashboardUpdate',					'layout.json',			null],
		'dashboard.view'				=> ['CControllerDashboardView',						'layout.htmlpage',		'monitoring.dashboard.view'],
		'dashboard.widget.check'		=> ['CControllerDashboardWidgetCheck',				'layout.json',			null],
		'dashboard.widget.configure'	=> ['CControllerDashboardWidgetConfigure',			'layout.json',			null],
		'dashboard.widget.edit'			=> ['CControllerDashboardWidgetEdit',				'layout.json',			'monitoring.dashboard.widget.edit'],
		'dashboard.widget.rfrate'		=> ['CControllerDashboardWidgetRfRate',				'layout.json',			null],
		'discovery.view'				=> ['CControllerDiscoveryView',						'layout.htmlpage',		'monitoring.discovery.view'],
		'export.hosts.xml'				=> ['CControllerExportXml',							'layout.xml',			null],
		'export.mediatypes.xml'			=> ['CControllerExportXml',							'layout.xml',			null],
		'export.screens.xml'			=> ['CControllerExportXml',							'layout.xml',			null],
		'export.sysmaps.xml'			=> ['CControllerExportXml',							'layout.xml',			null],
		'export.templates.xml'			=> ['CControllerExportXml',							'layout.xml',			null],
		'export.valuemaps.xml'			=> ['CControllerExportXml',							'layout.xml',			null],
		'favourite.create'				=> ['CControllerFavouriteCreate',					'layout.javascript',	null],
		'favourite.delete'				=> ['CControllerFavouriteDelete',					'layout.javascript',	null],
		'gui.edit'						=> ['CControllerGuiEdit',							'layout.htmlpage',		'administration.gui.edit'],
		'gui.update'					=> ['CControllerGuiUpdate',							null,					null],
		'housekeeping.edit'				=> ['CControllerHousekeepingEdit',					'layout.htmlpage',		'administration.housekeeping.edit'],
		'housekeeping.update'			=> ['CControllerHousekeepingUpdate',				null,					null],
		'iconmap.create'				=> ['CControllerIconMapCreate',						null,					null],
		'iconmap.delete'				=> ['CControllerIconMapDelete',						null,					null],
		'iconmap.edit'					=> ['CControllerIconMapEdit',						'layout.htmlpage',		'administration.iconmap.edit'],
		'iconmap.list'					=> ['CControllerIconMapList',						'layout.htmlpage',		'administration.iconmap.list'],
		'iconmap.update'				=> ['CControllerIconMapUpdate',						null,					null],
		'image.create'					=> ['CControllerImageCreate',						null,					null],
		'image.delete'					=> ['CControllerImageDelete',						null,					null],
		'image.edit'					=> ['CControllerImageEdit',							'layout.htmlpage',		'administration.image.edit'],
		'image.list'					=> ['CControllerImageList',							'layout.htmlpage',		'administration.image.list'],
		'image.update'					=> ['CControllerImageUpdate',						null,					null],
		'latest.view'					=> ['CControllerLatestView',						'layout.htmlpage',		'monitoring.latest.view'],
		'latest.view.refresh'			=> ['CControllerLatestViewRefresh',					'layout.json',			'monitoring.latest.view.refresh'],
		'macros.edit'					=> ['CControllerMacrosEdit',						'layout.htmlpage',		'administration.macros.edit'],
		'macros.update'					=> ['CControllerMacrosUpdate',						null,					null],
		'map.view'						=> ['CControllerMapView',							'layout.htmlpage',		'monitoring.map.view'],
		'mediatype.create'				=> ['CControllerMediatypeCreate',					null,					null],
		'mediatype.delete'				=> ['CControllerMediatypeDelete',					null,					null],
		'mediatype.disable'				=> ['CControllerMediatypeDisable',					null,					null],
		'mediatype.edit'				=> ['CControllerMediatypeEdit',						'layout.htmlpage',		'administration.mediatype.edit'],
		'mediatype.enable'				=> ['CControllerMediatypeEnable',					null,					null],
		'mediatype.list'				=> ['CControllerMediatypeList',						'layout.htmlpage',		'administration.mediatype.list'],
		'mediatype.update'				=> ['CControllerMediatypeUpdate',					null,					null],
		'menu.popup'					=> ['CControllerMenuPopup',							'layout.json',			null],
		'miscconfig.edit'				=> ['CControllerMiscConfigEdit',					'layout.htmlpage',		'administration.miscconfig.edit'],
		'miscconfig.update'				=> ['CControllerMiscConfigUpdate',					null,					null],
		'notifications.get'				=> ['CControllerNotificationsGet',					'layout.json',			null],
		'notifications.mute'			=> ['CControllerNotificationsMute',					'layout.json',			null],
		'notifications.read'			=> ['CControllerNotificationsRead',					'layout.json',			null],
		'popup.action.acknowledge'		=> ['CControllerPopupActionAcknowledge',			'layout.json',			'popup.operation.common'],
		'popup.action.operation'		=> ['CControllerPopupActionOperation',				'layout.json',			'popup.operation.common'],
		'popup.action.recovery'			=> ['CControllerPopupActionRecovery',				'layout.json',			'popup.operation.common'],
		'popup.condition.actions'		=> ['CControllerPopupConditionActions',				'layout.json',			'popup.condition.common'],
		'popup.condition.event.corr'	=> ['CControllerPopupConditionEventCorr',			'layout.json',			'popup.condition.common'],
		'popup.condition.operations'	=> ['CControllerPopupConditionOperations',			'layout.json',			'popup.condition.common'],
		'popup.discovery.check'			=> ['CControllerPopupDiscoveryCheck',				'layout.json',			'popup.discovery.check'],
		'popup.generic'					=> ['CControllerPopupGeneric',						'layout.json',			'popup.generic'],
		'popup.httpstep'				=> ['CControllerPopupHttpStep',						'layout.json',			'popup.httpstep'],
		'popup.maintenance.period'		=> ['CControllerPopupMaintenancePeriod',			'layout.json',			'popup.maintenance.period'],
		'popup.media'					=> ['CControllerPopupMedia',						'layout.json',			'popup.media'],
		'popup.mediatypetest.edit'		=> ['CControllerPopupMediatypeTestEdit',			'layout.json',			'popup.mediatypetest.edit'],
		'popup.mediatypetest.send'		=> ['CControllerPopupMediatypeTestSend',			'layout.json',			null],
		'popup.preproctest.edit'		=> ['CControllerPopupPreprocTestEdit',				'layout.json',			'popup.preproctestedit.view'],
		'popup.preproctest.send'		=> ['CControllerPopupPreprocTestSend',				'layout.json',			null],
		'popup.scriptexec'				=> ['CControllerPopupScriptExec',					'layout.json',			'popup.scriptexec'],
		'popup.services'				=> ['CControllerPopupServices',						'layout.json',			'popup.services'],
		'popup.testtriggerexpr'			=> ['CControllerPopupTestTriggerExpr',				'layout.json',			'popup.testtriggerexpr'],
		'popup.triggerexpr'				=> ['CControllerPopupTriggerExpr',					'layout.json',			'popup.triggerexpr'],
		'popup.triggerwizard'			=> ['CControllerPopupTriggerWizard',				'layout.json',			'popup.triggerwizard'],
		'problem.view'					=> ['CControllerProblemView',						'layout.htmlpage',		'monitoring.problem.view'],
		'problem.view.csv'				=> ['CControllerProblemView',						'layout.csv',			'monitoring.problem.view'],
		'profile.update'				=> ['CControllerProfileUpdate',						'layout.json',			null],
		'proxy.create'					=> ['CControllerProxyCreate',						null,					null],
		'proxy.delete'					=> ['CControllerProxyDelete',						null,					null],
		'proxy.edit'					=> ['CControllerProxyEdit',							'layout.htmlpage',		'administration.proxy.edit'],
		'proxy.hostdisable'				=> ['CControllerProxyHostDisable',					null,					null],
		'proxy.hostenable'				=> ['CControllerProxyHostEnable',					null,					null],
		'proxy.list'					=> ['CControllerProxyList',							'layout.htmlpage',		'administration.proxy.list'],
		'proxy.update'					=> ['CControllerProxyUpdate',						null,					null],
		'regex.create'					=> ['CControllerRegExCreate',						null,					null],
		'regex.delete'					=> ['CControllerRegExDelete',						null,					null],
		'regex.edit'					=> ['CControllerRegExEdit',							'layout.htmlpage',		'administration.regex.edit'],
		'regex.list'					=> ['CControllerRegExList',							'layout.htmlpage',		'administration.regex.list'],
		'regex.test'					=> ['CControllerRegExTest',							null,					null],
		'regex.update'					=> ['CControllerRegExUpdate',						null,					null],
		'report.services'				=> ['CControllerReportServices',					'layout.htmlpage',		'report.services'],
		'report.status'					=> ['CControllerReportStatus',						'layout.htmlpage',		'report.status'],
		'script.create'					=> ['CControllerScriptCreate',						null,					null],
		'script.delete'					=> ['CControllerScriptDelete',						null,					null],
		'script.edit'					=> ['CControllerScriptEdit',						'layout.htmlpage',		'administration.script.edit'],
		'script.list'					=> ['CControllerScriptList',						'layout.htmlpage',		'administration.script.list'],
		'script.update'					=> ['CControllerScriptUpdate',						null,					null],
		'search'						=> ['CControllerSearch',							'layout.htmlpage',		'search'],
		'system.warning'				=> ['CControllerSystemWarning',						'layout.warning',		'system.warning'],
		'timeselector.update'			=> ['CControllerTimeSelectorUpdate',				'layout.json',			null],
		'trigdisplay.edit'				=> ['CControllerTrigDisplayEdit',					'layout.htmlpage',		'administration.trigdisplay.edit'],
		'trigdisplay.reset'				=> ['CControllerTrigDisplayReset',					null,					null],
		'trigdisplay.update'			=> ['CControllerTrigDisplayUpdate',					null,					null],
		'trigseverity.edit'				=> ['CControllerTrigSeverityEdit',					'layout.htmlpage',		'administration.trigseverity.edit'],
		'trigseverity.reset'			=> ['CControllerTrigSeverityReset',					null,					null],
		'trigseverity.update'			=> ['CControllerTrigSeverityUpdate',				null,					null],
		'user.create'					=> ['CControllerUserCreate',						null,					null],
		'user.delete'					=> ['CControllerUserDelete',						null,					null],
		'user.edit'						=> ['CControllerUserEdit',							'layout.htmlpage',		'administration.user.edit'],
		'user.list'						=> ['CControllerUserList',							'layout.htmlpage',		'administration.user.list'],
		'user.unblock'					=> ['CControllerUserUnblock',						null,					null],
		'user.update'					=> ['CControllerUserUpdate',						null,					null],
		'usergroup.create'				=> ['CControllerUsergroupCreate',					null,					null],
		'usergroup.delete'				=> ['CControllerUsergroupDelete',					null,					null],
		'usergroup.edit'				=> ['CControllerUsergroupEdit',						'layout.htmlpage',		'administration.usergroup.edit'],
		'usergroup.groupright.add'		=> ['CControllerUsergroupGrouprightAdd',			'layout.json',			'administration.usergroup.grouprights'],
		'usergroup.list'				=> ['CControllerUsergroupList',						'layout.htmlpage',		'administration.usergroup.list'],
		'usergroup.massupdate'			=> ['CControllerUsergroupMassUpdate',				null,					null],
		'usergroup.tagfilter.add'		=> ['CControllerUsergroupTagfilterAdd',				'layout.json',			'administration.usergroup.tagfilters'],
		'usergroup.update'				=> ['CControllerUsergroupUpdate',					null,					null],
		'userprofile.edit'				=> ['CControllerUserProfileEdit',					'layout.htmlpage',		'administration.user.edit'],
		'userprofile.update'			=> ['CControllerUserProfileUpdate',					null,					null],
		'valuemap.create'				=> ['CControllerValuemapCreate',					null,					null],
		'valuemap.delete'				=> ['CControllerValuemapDelete',					null,					null],
		'valuemap.edit'					=> ['CControllerValuemapEdit',						'layout.htmlpage',		'administration.valuemap.edit'],
		'valuemap.list'					=> ['CControllerValuemapList',						'layout.htmlpage',		'administration.valuemap.list'],
		'valuemap.update'				=> ['CControllerValuemapUpdate',					null,					null],
		'web.view'						=> ['CControllerWebView',							'layout.htmlpage',		'monitoring.web.view'],
		'widget.actionlog.view'			=> ['CControllerWidgetActionLogView',				'layout.widget',		'monitoring.widget.actionlog.view'],
		'widget.clock.view'				=> ['CControllerWidgetClockView',					'layout.widget',		'monitoring.widget.clock.view'],
		'widget.dataover.view'			=> ['CControllerWidgetDataOverView',				'layout.widget',		'monitoring.widget.dataover.view'],
		'widget.discovery.view'			=> ['CControllerWidgetDiscoveryView',				'layout.widget',		'monitoring.widget.discovery.view'],
		'widget.favgraphs.view'			=> ['CControllerWidgetFavGraphsView',				'layout.widget',		'monitoring.widget.favgraphs.view'],
		'widget.favmaps.view'			=> ['CControllerWidgetFavMapsView',					'layout.widget',		'monitoring.widget.favmaps.view'],
		'widget.favscreens.view'		=> ['CControllerWidgetFavScreensView',				'layout.widget',		'monitoring.widget.favscreens.view'],
		'widget.graph.view'				=> ['CControllerWidgetGraphView',					'layout.widget',		'monitoring.widget.graph.view'],
		'widget.graphprototype.view'	=> ['CControllerWidgetIteratorGraphPrototypeView',	'layout.json',			null],
		'widget.hostavail.view'			=> ['CControllerWidgetHostAvailView',				'layout.widget',		'monitoring.widget.hostavail.view'],
		'widget.map.view'				=> ['CControllerWidgetMapView',						'layout.widget',		'monitoring.widget.map.view'],
		'widget.navtree.item.edit'		=> ['CControllerWidgetNavTreeItemEdit',				'layout.json',			null],
		'widget.navtree.item.update'	=> ['CControllerWidgetNavTreeItemUpdate',			'layout.json',			null],
		'widget.navtree.view'			=> ['CControllerWidgetNavTreeView',					'layout.widget',		'monitoring.widget.navtree.view'],
		'widget.plaintext.view'			=> ['CControllerWidgetPlainTextView',				'layout.widget',		'monitoring.widget.plaintext.view'],
		'widget.problemhosts.view'		=> ['CControllerWidgetProblemHostsView',			'layout.widget',		'monitoring.widget.problemhosts.view'],
		'widget.problems.view'			=> ['CControllerWidgetProblemsView',				'layout.widget',		'monitoring.widget.problems.view'],
		'widget.problemsbysv.view'		=> ['CControllerWidgetProblemsBySvView',			'layout.widget',		'monitoring.widget.problemsbysv.view'],
		'widget.svggraph.view'			=> ['CControllerWidgetSvgGraphView',				'layout.widget',		'monitoring.widget.svggraph.view'],
		'widget.systeminfo.view'		=> ['CControllerWidgetSystemInfoView',				'layout.widget',		'monitoring.widget.systeminfo.view'],
		'widget.trigover.view'			=> ['CControllerWidgetTrigOverView',				'layout.widget',		'monitoring.widget.trigover.view'],
		'widget.url.view'				=> ['CControllerWidgetUrlView',						'layout.widget',		'monitoring.widget.url.view'],
		'widget.web.view'				=> ['CControllerWidgetWebView',						'layout.widget',		'monitoring.widget.web.view'],
		'workingtime.edit'				=> ['CControllerWorkingTimeEdit',					'layout.htmlpage',		'administration.workingtime.edit'],
		'workingtime.update'			=> ['CControllerWorkingTimeUpdate',					null,					null]
	];

	public function __construct($action) {
		$this->action = $action;
		$this->calculateRoute();
	}

	/**
	 * Locate and set controller, layout and view by action name.
	 *
	 * @return string
	 */
	public function calculateRoute() {
		if (array_key_exists($this->action, $this->routes)) {
			$this->controller = $this->routes[$this->action][0];
			$this->layout = $this->routes[$this->action][1];
			$this->view = $this->routes[$this->action][2];
		}
	}

	/**
	 * Returns layout name.
	 *
	 * @return string
	 */
	public function getLayout() {
		return $this->layout;
	}

	/**
	 * Returns controller name.
	 *
	 * @return string
	 */
	public function getController() {
		return $this->controller;
	}

	/**
	 * Returns view name.
	 *
	 * @return string
	 */
	public function getView() {
		return $this->view;
	}

	/**
	 * Returns action name.
	 *
	 * @return string
	 */
	public function getAction() {
		return $this->action;
	}
}

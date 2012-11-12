Ext.onReady(function() {
    MODx.load({ xtype: 'xodus-page-home'});
});
 
Xodus.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
		id:'xodus-page-home'
		,formpanel:'xodus-panel-xodus'
        ,url: Xodus.config.connectorUrl
		,baseParams: { action: 'mgr/xodus/getfile' }
		,components: [{
            title:_('xodus.export')
				,xtype: 'xodus-panel-xodus'
               // ,cls: 'main-wrapper'
            ,renderTo: 'xodus-panel-home-div'
        }]
		,buttons:
    	[{
        	process:'mgr/xodus/getfile'
			,url: Xodus.config.connectorUrl
			,text: _('xodus.export')
			,params: { action: 'mgr/xodus/getfile' }
			,method: 'remote'
            ,checkDirty: false
            ,keys: [{
                key: MODx.config.keymap_save || 's'
                ,alt: true
                ,ctrl: true
            }]
		},'-',{
            process: 'cancel'
            ,text: _('xodus.default_back')
            ,params: { a:MODx.action['controllers/index'] }
        }]
    });
    Xodus.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(Xodus.page.Home,MODx.Component);
Ext.reg('xodus-page-home',Xodus.page.Home);
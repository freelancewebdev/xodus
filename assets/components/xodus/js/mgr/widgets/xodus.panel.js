Xodus.panel.Xodus = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'xodus-panel-xodus'
        ,url: Xodus.config.connectorUrl
		,baseParams: { action: 'mgr/xodus/getfile', limit: '0' }
        ,anchor: '98%'
		,layout:'form'
		,baseCls:'modx-formpanel'
		,cls: 'container form-with-labels'
        ,listeners: {
			'success' : {
				fn:this.success,
				scope:this	
			}
		}
		,items:[{
			html: '<h2>'+_('xodus.desc')+'</h2><p>' + _('xodus.intro') + '<br/><br/></p>'
			,border: false
            ,cls: 'modx-page-header'
        	},
			{xtype:'modx-combo-usergroup',name:'group',fieldLabel:_('xodus.group'),allowBlank:false,forceSelection:true,blankText:_('xodus.error_group'),hiddenName:'group'
			}
			,{
				fieldLabel:_('xodus.format')
				,header:_('xodus.format')
				,xtype:'combo'
      			,name:'format'
				,hiddenName:'format'
				,id:'format'
      			,store: new Ext.data.ArrayStore({
        			id:      0
        			,fields:  [ 'formatID', 'displayText' ]
       				,data: [ [ 0, 'CSV' ], [ 1, 'Excel 2000' ], [ 2, 'Excel 2007' ] ]
      			})
				,triggerAction: 'all'
     			,typeAhead:false
      			,mode: 'local'
      			,valueField:'formatID'
      			,displayField:'displayText'
      			,allowBlank:false
				,blankText:_('xodus.error_file_format')
				,editable:false
      			,forceSelection: true	
			}]
	}
	);
    Xodus.panel.Xodus.superclass.constructor.call(this,config)
};
Ext.extend(Xodus.panel.Xodus,MODx.FormPanel,{
		initialized: false,
		success:function(o){
			MODx.msg.status({
            	title: _('xodus'),
            	message: o.result.message,
            	delay: 3
        	});
			if(o.result.object.total > 0){
				location.href = o.result.object.action_url + '&f=' + o.result.object.file;
			}
        }
	});
Ext.reg('xodus-panel-xodus',Xodus.panel.Xodus);
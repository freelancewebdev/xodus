var Xodus = function(config) {
    config = config || {};
    Xodus.superclass.constructor.call(this,config);
};
Ext.extend(Xodus,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {}
});
Ext.reg('xodus',Xodus);
Xodus = new Xodus();
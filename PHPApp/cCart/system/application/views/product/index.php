<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/ext/resources/css/ext-all.css"/>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/ext/adapter/ext/ext-base.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/ext/ext-all.js"></script>
    <!-- product data view style -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/ext/ux/data-view.css"/>

    <script type="text/javascript">
    var BASE_URL = '<?php echo site_url(); ?>/';

    Ext.onReady(function() {

        var proxyProduct = new Ext.data.HttpProxy({
            url: BASE_URL + 'product/ext_get_all', method: 'POST'
        });

        var strProduct = new Ext.data.JsonStore({
            proxy: proxyProduct,
            root: 'products',
            fields: [
                'id', 'name', 'price', 'image'
            ]
        });

        strProduct.load();
        
        var tplProduct = new Ext.XTemplate(
            '<tpl for=".">',
                '<div class="thumb-wrap" id="{name}">',
                '<div class="thumb"><img src="http://localhost/_DEMO.NITT/AaaCart/assets/img/{image}" title="{name}"></div>',
                '<span class="name">{name}</span>',
                '<div class="price">$ {price}</div></div>',
            '</tpl>',
            '<div class="x-clear"></div>'
        ); 

        var dvProduct = new Ext.DataView({
            autoScroll: true, store: strProduct, tpl: tplProduct,
            autoHeight: false, height: 200, multiSelect: false,
            overClass: 'x-view-over', itemSelector: 'div.thumb-wrap',
            emptyText: 'No product to display',
            style: 'border:1px solid #99BBE8;',
            listeners: {
                render: initializeItemDragZone
            }
        });

        function initializeItemDragZone(v) {
            v.dragZone = new Ext.dd.DragZone(v.getEl(), {
                getDragData: function(e) {
                    var sourceEl = e.getTarget(v.itemSelector, 10);
                    if (sourceEl) {
                        d = sourceEl.cloneNode(true);
                        d.id = Ext.id();
                        return v.dragData = {
                            sourceEl: sourceEl,
                            repairXY: Ext.fly(sourceEl).getXY(),
                            ddel: d,
                            itemData: v.getRecord(sourceEl).data
                        }
                    }
                },

                getRepairXY: function() {
                    return this.dragData.repairXY;
                }
            });
        }

        var panelProduct = new Ext.Panel({
            id: 'images-view',
            frame: true,
            width: 620,
            autoHeight: true,
            title: 'Product DataView',
            style: 'margin:0 auto;',
            items: [dvProduct]
        });

        panelProduct.render('top');

        var strCart = new Ext.data.JsonStore({
            root: 'cart',
            fields: [
                'rowid', 'id', 'qty', 'name', 'price', 'subtotal'
            ],
            proxy: new Ext.data.HttpProxy({
                url: BASE_URL + 'product/ext_get_cart', method: 'POST'
            })
        });
        strCart.load();

        var cb_select = new Ext.grid.CheckboxSelectionModel();

        function showDollar(val) {
            if (val == '' || val == '<b>Total:</b>') {
                return val;
            }
            else {
                return '$ ' + val;
            }
        }

        var panelCart = new Ext.grid.EditorGridPanel({
            frame: true, border: true, stripeRows: true, 
            store: strCart, loadMask: true, title: 'Your Cart (Drag Here)',
            style: 'margin:0 auto;font-size:13px;',
            height: 220, width: 500, sm: cb_select,
            columns: [{
                header: 'rowid',
                dataIndex: 'rowid',
                hidden: true,
                hideable: false
            }, {
                header: 'id',
                dataIndex: 'id',
                hidden: true,
                hideable: false
            }, {
                header: "Product Name",
                dataIndex: 'name',
                sortable: true,
                width: 280
            }, {
                header: "Qty",
                align: 'center',
                width: 40,
                dataIndex: 'qty',
                menuDisabled: true,
                editor: new Ext.form.TextField({
                    allowBlank: false,
                    vtype: 'alphanum',
                    id: 'qtyField'
                })
            }, {
                header: "Price",
                dataIndex: 'price',
                sortable: true,
                align: 'right',
                renderer: showDollar,
                width: 80
            }, {
                header: "Subtotal",
                sortable: true,
                align: 'right',
                width: 80,
                renderer: showDollar
            }],
            listeners: {
                'afteredit': function() {
                    var sm = panelCart.getSelectionModel().getSelections();
                    panelCart.getSelectionModel().clearSelections();
                    Ext.Ajax.request({
                        method: 'POST',
                        url: BASE_URL + 'product/ext_update_cart',
                        params: {
                            rowid: sm[0].get('rowid'),
                            qty: Ext.getCmp('qtyField').getValue()
                        },
                        success: function() {
                            strCart.load();
                        }
                    });
                }
            },
            buttons: [{
                text: 'Clear Cart',
                handler: function() {
                    Ext.Ajax.request({
                        url: BASE_URL + 'product/ext_clear_cart',
                        method: 'POST',
                        success: function() {
                            strCart.load();
                        }
                    });
                }
            }, {
                text: 'Check Out',
                handler: function() {
                    
                }
            }]
        });

        panelCart.render('bottom');

        var formPanelDropTargetEl =  panelCart.getView().scroller.dom;

        var formPanelDropTarget = new Ext.dd.DropTarget(formPanelDropTargetEl, {
            notifyDrop  : function(ddSource, e, data){
                panelCart.getSelectionModel().selectAll();
                var sm = panelCart.getSelectionModel().getSelections();
                panelCart.getSelectionModel().clearSelections();

                data.itemData.rowid = '';
                for (i=0; i<=sm.length-1; i++) {
                    if (sm[i].get('id') == data.itemData.id) {
                        data.itemData.rowid = sm[i].get('rowid');
                        data.itemData.qty = sm[i].get('qty');
                        // so can out from loop
                        i = sm.length;
                    }
                }
                                
                Ext.Ajax.request({
                    url: BASE_URL + 'product/ext_add_cart',
                    method: 'POST',
                    params: {
                        'id': data.itemData.id,
                        'price': data.itemData.price,
                        'name': data.itemData.name,
                        'rowid': data.itemData.rowid,
                        'qty': data.itemData.qty
                    },
                    success: function() {
                        strCart.load();
                    }
                });
                return(true);
            }
        });
    });
    </script>
    <title>Extjs Image Gallery Using DataView</title>
    <style type="text/css">
        body {
            padding: 20px;
            margin: 0 auto;
        }
        #container {
            padding: 10px;
            background: #e3e3e3;
            border: 1px solid #d3d3d3;
            margin: 0 auto;
            text-align: left;
            width: 630px;
        }
        #top {
            
        }
        #bottom {
            margin-top: 10px;
        }
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  </head>
  <body>
      <div id="container">
          <div id="top"></div>
          <div id="bottom"></div>
      </div>
  </body>
</html>

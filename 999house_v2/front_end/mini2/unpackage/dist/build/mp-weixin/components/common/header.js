(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["components/common/header"],{"3f05":function(o,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var a={data:function(){return{scrollFlag:!1,backcolor:!1,backClass:"",mapColor:""}},props:["location","isfixed"],watch:{isfixed:function(o){o?(this.backcolor=!0,this.backClass="#F8F8F8",this.mapColor="#000"):(this.backcolor=!1,this.backClass=this.mapColor="")}},created:function(){this.$api.localStore.localDel("pre-page")},methods:{chooseLocation:function(){this.goPage("index/location")},goSearch:function(){console.log(window),window&&this.$api.localStore.localSet("pre-page",window.location.href),this.goPage("index/search")}}};t.default=a},"59ffd":function(o,t,n){"use strict";n.r(t);var a=n("3f05"),c=n.n(a);for(var e in a)"default"!==e&&function(o){n.d(t,o,(function(){return a[o]}))}(e);t["default"]=c.a},"8a65":function(o,t,n){},9336:function(o,t,n){"use strict";var a=n("8a65"),c=n.n(a);c.a},b5b8:function(o,t,n){"use strict";var a;n.d(t,"b",(function(){return c})),n.d(t,"c",(function(){return e})),n.d(t,"a",(function(){return a}));var c=function(){var o=this,t=o.$createElement;o._self._c},e=[]},c8d9:function(o,t,n){"use strict";n.r(t);var a=n("b5b8"),c=n("59ffd");for(var e in c)"default"!==e&&function(o){n.d(t,o,(function(){return c[o]}))}(e);n("9336");var i,r=n("f0c5"),l=Object(r["a"])(c["default"],a["b"],a["c"],!1,null,null,null,!1,a["a"],i);t["default"]=l.exports}}]);
;(global["webpackJsonp"] = global["webpackJsonp"] || []).push([
    'components/common/header-create-component',
    {
        'components/common/header-create-component':(function(module, exports, __webpack_require__){
            __webpack_require__('543d')['createComponent'](__webpack_require__("c8d9"))
        })
    },
    [['components/common/header-create-component']]
]);

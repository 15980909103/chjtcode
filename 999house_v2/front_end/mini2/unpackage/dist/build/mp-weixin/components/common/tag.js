(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["components/common/tag"],{"1d08":function(t,n,e){},"4a37":function(t,n,e){"use strict";var u;e.d(n,"b",(function(){return a})),e.d(n,"c",(function(){return r})),e.d(n,"a",(function(){return u}));var a=function(){var t=this,n=t.$createElement,e=(t._self._c,Number(t.myTag));t.$mp.data=Object.assign({},{$root:{m0:e}})},r=[]},b2f7:function(t,n,e){"use strict";var u=e("1d08"),a=e.n(u);a.a},b97d:function(t,n,e){"use strict";e.r(n);var u=e("4a37"),a=e("d54d");for(var r in a)"default"!==r&&function(t){e.d(n,t,(function(){return a[t]}))}(r);e("b2f7");var i,c=e("f0c5"),o=Object(c["a"])(a["default"],u["b"],u["c"],!1,null,null,null,!1,u["a"],i);n["default"]=o.exports},c7b2:function(t,n,e){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var u={data:function(){return{myTag:-1}},props:{list:{type:[Array],default:function(){return[]}},marginRight:{type:[String,Number],default:function(){return 0}},tag:{type:[String,Number],default:function(){return-1}},cancel:{type:Boolean,default:function(){return!0}},bg:{type:Boolean,default:function(){return!1}},start:{type:[String,Number],default:function(){return 0}}},watch:{tag:function(t){this.myTag=t}},created:function(){this.myTag=this.tag},methods:{chooseTip:function(t){if(t==this.myTag){if(!this.cancel)return;this.myTag=-1}else this.myTag=t;this.$emit("change",this.myTag)}}};n.default=u},d54d:function(t,n,e){"use strict";e.r(n);var u=e("c7b2"),a=e.n(u);for(var r in u)"default"!==r&&function(t){e.d(n,t,(function(){return u[t]}))}(r);n["default"]=a.a}}]);
;(global["webpackJsonp"] = global["webpackJsonp"] || []).push([
    'components/common/tag-create-component',
    {
        'components/common/tag-create-component':(function(module, exports, __webpack_require__){
            __webpack_require__('543d')['createComponent'](__webpack_require__("b97d"))
        })
    },
    [['components/common/tag-create-component']]
]);

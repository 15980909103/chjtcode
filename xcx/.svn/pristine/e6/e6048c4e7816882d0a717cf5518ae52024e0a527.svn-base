<script>
export default {
  name: 'MenuItem',
  functional: true,
  props: {
    meta: {
      type: Object,
      default: () => {
        return {
          title: '',
          icon: ''
        }
      }
    }
  },
  render(h, context) {
    const vnodes = []
    const { icon, title,pending_disposal,id } = context.props.meta
    if (icon) {
      if(icon.indexOf("el-icon") != '-1'){
        vnodes.push(<i class={icon}></i>)
      }else{
        vnodes.push(<svg-icon icon-class={icon}/>)
      }
    }
    if (title) {
      let ids = [41,40,36,45,46];
      if(ids.includes(id)){
        if(pending_disposal){
          vnodes.push(<span slot='title'>{(title)}             <el-badge class="mark" value={(pending_disposal)}/></span>)
        }else{
          vnodes.push(<span slot='title'>{(title)}</span>)
        }
      }else{
        vnodes.push(<span slot='title'>{(title)}</span>)
      }

    }
    return vnodes
  }
}
</script>
<style rel="stylesheet/scss" lang="scss">
  .mark {
    sup{
      background-color: #df3031 !important;
      border: none;
    }
  }
</style>

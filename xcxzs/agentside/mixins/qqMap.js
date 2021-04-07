const qqMapMixins = {
  props: {
    mainIcon: {
      type: String,
      default: '../../static/image/map-marker-icon.png',
    },
  },
  data: {
    map: null,
    cluster: null,
    currentMainMarker: -1,
    centerMarker: null,
    itemIconList:{
      bus:'../../static/image/l-bus-icon.png',
      study:'../../static/image/l-study-icon.png',
      hospital:'../../static/image/l-hospital-icon.png',
      shop:'../../static/image/l-shop-icon.png',
      food:'../../static/image/l-food-icon.png',
    },
    buildIcon:'../../static/image/l-bulid-icon.png',
    current_itemType:'',
  },
  watch: {
    currentMainMarker(value, old) {
      this.toggleCurrentMainMarker(value, old);
    }
  },
  methods: {
    initMap({lat = null, lng = null}, mapSelector, mapOptions, centerMarkOptions) {
      const centerMark = new qq.maps.LatLng(lat, lng);
      const map = new qq.maps.Map(document.getElementById(mapSelector),{
          center: centerMark,
          disableDefaultUI:true,
          zoom: 15,
          ...mapOptions
      });
      this.map = map;

      // center marker
      /* const marker = new qq.maps.Marker({
          position: centerMark,
          map: map,
          animation: qq.maps.MarkerAnimation.BOUNCE,
          icon: new qq.maps.MarkerImage(this.mainIcon),
          ...centerMarkOptions
      });
      this.centerMarker = marker; */
      
      this.initCluster();
      this.centerMarker = this.displayBuildMarker({lat: lat, lng: lng})
    },
    initCluster() {
        this.cluster = new qq.maps.MarkerCluster({
            map: this.map,
            minimumClusterSize: 0,
            maxZoom: 10,
            markers: new qq.maps.MVCArray()
        });
        window.cluster = this.cluster
    },
    displayMarkers(e) {
        this.cluster.clearMarkers();
        this.current_itemType = e.itemType
        e.data.forEach((item, index) => {
          var marker = new qq.maps.Marker({
              map: this.map,
              position: new qq.maps.LatLng(item.lat, item.lng),
              icon: new qq.maps.MarkerImage(this.itemIconList[e.itemType],new qq.maps.Size(33, 33)),
          });
          this.cluster.addMarker(marker);
        });
    },
    displayBuildMarker(e){
      var marker = new qq.maps.Marker({
          map: this.map,
          position: new qq.maps.LatLng(e.lat, e.lng),
          icon: new qq.maps.MarkerImage(this.buildIcon,new qq.maps.Size(33, 33)),
      });
      return marker
    },
    toggleCurrentMapCenter(lat, lng) {
      this.map.setCenter(new qq.maps.LatLng(lat, lng));
    },
    toggleCurrentMainMarker(index, oldIndex) {
      const markers = this.cluster.getMarkers().elems;
      const getIndexMarker = (index) => {
        return index == -1? this.centerMarker: markers[index];
      }
      const target = getIndexMarker(index);
      const current = getIndexMarker(oldIndex);

      current.setAnimation(null);
      target.setAnimation(qq.maps.MarkerAnimation.BOUNCE);

      if(oldIndex!='-1'){
        current.setIcon(new qq.maps.MarkerImage(this.itemIconList[this.current_itemType],new qq.maps.Size(33, 33)));
      }
      
      target.setIcon(new qq.maps.MarkerImage(this.mainIcon),new qq.maps.Size(33, 33));

      targetPosition = target.getPosition();
      this.toggleCurrentMapCenter(targetPosition.lat, targetPosition.lng);
    }
  }
};

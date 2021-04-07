const listenInputClear = {
  data: {
    isLestenInputClear: false,
  },
  methods: {
    listenInputTaPClearIcon() {
        if (!this.isLestenInputClear) {
            const icon = mui(".mui-icon-clear")[0];
            icon.addEventListener('tap', () => {
                this.searchText = '';
            })
            this.isLestenInputClear = true;
        }
    }
  }
};

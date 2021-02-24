<script>
    window.__vueList = {
        data: {
            searchForm: {},
            currentPage: 1,
            totalCount: 0,
            pageCount: 0,
            pageSize: 20,
        },
        methods: {
            currentPageChange: function (e) {
                this.currentPage = e;
                this.getList();
            },
            handListData: function (res) {
                var data = res.data;
                this.lists = data.data;
                this.totalCount = data.total;
                this.pageSize = data.per_page;
                this.pageCount = data.last_page;
                this.currentPage = data.current_page;
            }
        }
    }
</script>
<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index" id="app" v-cloak>
    <button class="btn btn-xs btn-success" id="0">Add</button>
    <div id="root">

    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>

<script>
    let app = new Vue({
        el: '#app',
        data: {
            categories: JSON.parse(`<?php echo json_encode($categories) ?>`),
            endReached: false
        },
        methods: {
            requestSubCategories(depth, e) {
                this.categories = this.categories.slice(0, depth + 1);
                this.getCategory(e.target.value);
            },
            getCategory(id = null) {
                axios
                    .get(`http://localhost:8080/index.php?r=category/getcategory&id=${id}`)
                    .then(res => {
                        this.endReached = false;
                        let subCategories = res.data;
                        if (res.data.length) {
                            return this.categories.push(res.data);
                        }
                        this.endReached = true

                    })
                    .catch(e => console.log(e))
            },
            storeCategory(e) {
                let parentId = e.target.id;
                axios
                    .get(`http://localhost:8080/index.php?r=category/storecategory`, {
                        params: {
                            parentId
                        }
                    })
                    .then(res => {
                        if (res.data.success) {
                            this.categories.push(res.data.category);
                            return this.draw();
                        }
                        alert('Something Went Wrong');
                    })
                    .catch(e => console.log(e))
            },
            draw() {
                $('#root').html('');
                this.categories.forEach(category => {
                    category.parent_id = (category.parent_id) ? category.parent_id : 'root';
                    this.nestCategory(category);
                });
                document.querySelectorAll('#app button.btn-xs').forEach(btn => {
                    btn.addEventListener('click', this.storeCategory);
                })
            },
            nestCategory(category) {
                if ($(`#${category.parent_id}`).length) {
                    $(`#${category.parent_id}`).append(`<ul><li id="${category.id}">${category.name} <button id="${category.id}" class="btn btn-xs btn-success">Add</button></li></ul>`)
                }
            }
        },
        mounted() {
            this.getCategory();
            this.draw();
        }
    })
</script>

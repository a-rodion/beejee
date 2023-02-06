function queryParams(params) {
    params.asc = (params.order === 'asc')
    delete params.order
    return params
}

function linkFormatter(value, row, index) {
    return '<a href="?r=task/update&id=' + value + '">edit</a>'
}

function completedFormatter(value, row, index) {
    if (value === true) {
        return '<div class="text-center"><i class="checkIcon"></i></div>'
    } else {
        return ''
    }
}

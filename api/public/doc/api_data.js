define({ "api": [
  {
    "type": "post",
    "url": "/list/city",
    "title": "1.获取城市列表",
    "name": "city",
    "group": "List",
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Array</p> ",
            "optional": false,
            "field": "city",
            "description": "<p>返回城市列表数组.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": [\n        {\n            \"id\": 4,\n            \"title\": \"北京\"\n        },\n        {\n            \"id\": 2,\n            \"title\": \"广州\"\n        },\n        {\n            \"id\": 3,\n            \"title\": \"武汉\"\n        },\n        {\n            \"id\": 1,\n            \"title\": \"深圳\"\n        }\n    ]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/List/ListController.php",
    "groupTitle": "List"
  },
  {
    "type": "post",
    "url": "/list/department",
    "title": "2.获取部门列表",
    "name": "department",
    "group": "List",
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Array</p> ",
            "optional": false,
            "field": "department",
            "description": "<p>返回部门列表数组.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": [\n        {\n            \"id\": 1,\n            \"title\": \"产品部\"\n        },\n        {\n            \"id\": 5,\n            \"title\": \"商务部\"\n        },\n        {\n            \"id\": 2,\n            \"title\": \"运营部\"\n        }\n    ]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/List/ListController.php",
    "groupTitle": "List"
  },
  {
    "type": "post",
    "url": "/list/permission",
    "title": "5.获取用户菜单",
    "name": "menu",
    "group": "List",
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>number</p> ",
            "optional": false,
            "field": "id",
            "description": "<p>权限id.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>number</p> ",
            "optional": false,
            "field": "inherit_id",
            "description": "<p>继承于.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "title",
            "description": "<p>权限标题.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "slug",
            "description": "<p>权限路由.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "sort",
            "description": "<p>排序.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "show",
            "description": "<p>是否作为菜单显示.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\n{\n    \"result\": 1,\n    \"data\": [\n       {\n\t        \"id\": 2,\n\t        \"inherit_id\": 1,\n\t        \"title\": \"查看用户信息\",\n\t        \"slug\": \"user.create\",\n\t        \"sort\": 2,\n\t        'show': \"1\"\n\t    }\n    ]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/List/ListController.php",
    "groupTitle": "List"
  },
  {
    "type": "post",
    "url": "/list/permission",
    "title": "4.获取权限列表",
    "name": "permission",
    "group": "List",
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Array</p> ",
            "optional": false,
            "field": "permission",
            "description": "<p>返回权限列表数组.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "inherit_id",
            "description": "<p>继承于.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "sort",
            "description": "<p>排序.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": [\n        {\n            \"id\": 10,\n            \"inherit_id\": 11\n            \"title\": \"dddd\",\n            \"sort\": 1\n        },\n        {\n            \"id\": 11,\n            \"inherit_id\": 0\n            \"title\": \"ddddsssss\",\n            \"sort\": 2\n        }\n    ]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/List/ListController.php",
    "groupTitle": "List"
  },
  {
    "type": "post",
    "url": "/list/position",
    "title": "3.获取职位列表",
    "name": "position",
    "group": "List",
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Array</p> ",
            "optional": false,
            "field": "position",
            "description": "<p>返回职位列表数组.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": [\n        {\n            \"id\": 2,\n            \"title\": \"Andorid\"\n        },\n        {\n            \"id\": 4,\n            \"title\": \"Html5\"\n        },\n        {\n            \"id\": 3,\n            \"title\": \"IOS\"\n        },\n        {\n            \"id\": 1,\n            \"title\": \"PHP\"\n        }\n    ]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/List/ListController.php",
    "groupTitle": "List"
  },
  {
    "type": "post",
    "url": "/log/export",
    "title": "2.导出日志",
    "name": "export",
    "group": "Log",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "username",
            "description": "<p>可选,登录用户名.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "object",
            "description": "<p>可选,操作对象.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "start",
            "description": "<p>可选,起始时间.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "end",
            "description": "<p>可选,结束时间.</p> "
          }
        ]
      }
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Log/LogController.php",
    "groupTitle": "Log"
  },
  {
    "type": "get",
    "url": "/captcha",
    "title": "1.获取验证码",
    "description": "<p>此接口用于生成登录验证码,直接将img的src属于指向这里即可。</p> ",
    "name": "captcha",
    "group": "Login",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "uniqid",
            "description": "<p>必填,前端生成一个32位唯一标识,用于记录验证码.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Image</p> ",
            "optional": false,
            "field": "image",
            "description": "<p>返回验证码图片.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n验证码",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/IndexController.php",
    "groupTitle": "Login"
  },
  {
    "type": "post",
    "url": "/login",
    "title": "2.提交登录",
    "name": "login",
    "group": "Login",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "username",
            "description": "<p>必填,用户名.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "password",
            "description": "<p>必填,密码.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "captcha",
            "description": "<p>必填,验证码.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "uniqid",
            "description": "<p>必填,唯一标识,用于验证验证码,与上面保持一致.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "token",
            "description": "<p>加密过的token字符串.以后每次请求都需要将token加入到header中.格式如下：Authorization: Bearer {yourtokenhere},或者将token=xxx置于url中传递,否则将没有权限访问.错误码401表示token过期或者被加入黑名单(退出登录),400表示无效token.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Int</p> ",
            "optional": false,
            "field": "uid",
            "description": "<p>用户ID</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": {\n        \"token\": \"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvbGFyYXZlbFwvcHVibGljXC9pbmRleC5waHBcL2xvZ2luIiwiaWF0IjoiMTQzMDkwNTQ4NyIsImV4cCI6IjE0MzA5MDkwODciLCJuYmYiOiIxNDMwOTA1NDg3IiwianRpIjoiYTQ4OWI3N2NmOWY4NmUxMWZjMWY1NTE3ZTQ4NjViZjYifQ.Njg2ZWQ3ZDNjZjFjMGY3ZGVmMDhmYjdkZjI0MDI2NTY4YjFjOTBmNzM4MzFhYzgzZjNkZTZmNTc3NGRhODI4Ng\",\n        \"uid\": 1\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"验证码错误\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/IndexController.php",
    "groupTitle": "Login"
  },
  {
    "type": "post",
    "url": "/logout",
    "title": "3.退出登录",
    "name": "logout",
    "group": "Login",
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"token无效\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/IndexController.php",
    "groupTitle": "Login"
  },
  {
    "type": "post",
    "url": "/log/index",
    "title": "1.日志列表",
    "name": "list",
    "group": "Log",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "username",
            "description": "<p>可选,登录用户名.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "object",
            "description": "<p>可选,操作对象.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "start",
            "description": "<p>可选,起始时间.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "end",
            "description": "<p>可选,结束时间.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "page",
            "description": "<p>可选,页数.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "page_size",
            "description": "<p>可选,分页大小.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "total",
            "description": "<p>总数据量.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "per_page",
            "description": "<p>分页大小.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "current_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "last_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "from",
            "description": "<p>起始数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "to",
            "description": "<p>结束数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "username",
            "description": "<p>登录用户名.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "roles",
            "description": "<p>用户角色.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "operation",
            "description": "<p>操作类型.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "Slug",
            "description": "<p>操作路径.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "object",
            "description": "<p>操作对象.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "ip",
            "description": "<p>操作IP.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "created_at",
            "description": "<p>操作时间.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\n{\n    \"result\": 1,\n    \"data\": {\n        \"total\": 5,\n        \"per_page\": 20,\n        \"current_page\": 1,\n        \"last_page\": 1,\n        \"from\": 1,\n        \"to\": 5,\n        \"data\": [\n            {\n                \"id\": 5,\n                \"username\": \"soothion\",\n                \"roles\": \"User\",\n                \"operation\": \"更新用户信息\",\n                \"slug\": \"user.update\",\n                \"object\": \"soothion\",\n                \"ip\": \"::1\",\n                \"created_at\": \"2015-05-11 07:29:48\"\n            }\n        ]\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Log/LogController.php",
    "groupTitle": "Log"
  },
  {
    "type": "post",
    "url": "/permission/create",
    "title": "4.创建权限",
    "name": "create",
    "group": "Permission",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "inherit_id",
            "description": "<p>继承于.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>权限标题.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "slug",
            "description": "<p>操作路径(路由名).</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "descrition",
            "description": "<p>描述信息.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "note",
            "description": "<p>备注信息.</p> "
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"权限创建失败\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Permission/PermissionController.php",
    "groupTitle": "Permission"
  },
  {
    "type": "post",
    "url": "/permission/export",
    "title": "5.导出权限",
    "name": "export",
    "group": "Permission",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "role_id",
            "description": "<p>可选,角色ID.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "department_id",
            "description": "<p>可选,部门ID.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>可选,用户状态.1正常、2停用、3注销.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "city_id",
            "description": "<p>可选,城市ID.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "start",
            "description": "<p>可选,起始时间.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "end",
            "description": "<p>可选,结束时间.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "keyword",
            "description": "<p>可选,搜索关键字,匹配帐号或者姓名.</p> "
          }
        ]
      }
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Permission/PermissionController.php",
    "groupTitle": "Permission"
  },
  {
    "type": "post",
    "url": "/permission/index",
    "title": "1.权限列表",
    "name": "list",
    "group": "Permission",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "role_id",
            "description": "<p>可选,角色ID.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "department_id",
            "description": "<p>可选,部门ID.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>可选,用户状态.1正常、2停用、3注销.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "city_id",
            "description": "<p>可选,城市ID.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "start",
            "description": "<p>可选,起始时间.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "end",
            "description": "<p>可选,结束时间.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "keyword",
            "description": "<p>可选,搜索关键字,匹配帐号或者姓名.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "page",
            "description": "<p>可选,页数.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "page_size",
            "description": "<p>可选,分页大小.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "total",
            "description": "<p>总数据量.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "per_page",
            "description": "<p>分页大小.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "current_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "last_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "from",
            "description": "<p>起始数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "to",
            "description": "<p>结束数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>用户姓名.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>用户状态.1正常、2停用、3注销.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "created_at",
            "description": "<p>创建时间.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "slug",
            "description": "<p>操作路径(路由名).</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "description",
            "description": "<p>描述信息.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": {\n        \"total\": 4,\n        \"per_page\": 20,\n        \"current_page\": 1,\n        \"last_page\": 1,\n        \"from\": 1,\n        \"to\": 4,\n        \"data\": [\n            {\n                \"title\": \"\",\n                \"result\": \"1\",\n                \"created_at\": \"0000-00-00 00:00:00\",\n                \"slug\": \"\",\n                \"description\": null\n            }\n        ]\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Permission/PermissionController.php",
    "groupTitle": "Permission"
  },
  {
    "type": "post",
    "url": "/user/show/:id",
    "title": "2.查看权限",
    "name": "show",
    "group": "Permission",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "id",
            "description": "<p>必填,用户ID.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "id",
            "description": "<p>ID.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "inherit_id",
            "description": "<p>继承于.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>权限标题.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "slug",
            "description": "<p>操作路径(路由名).</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "descrition",
            "description": "<p>描述信息.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "note",
            "description": "<p>备注信息.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "created_at",
            "description": "<p>创建时间.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "updated_at",
            "description": "<p>更新时间.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": {\n        \"id\": 1,\n        \"inherit_id\": null,\n        \"name\": \"查看用户列表1\",\n        \"slug\": null,\n        \"result\": \"1\",\n        \"description\": \"查看用户列表\",\n        \"note\": null,\n        \"created_at\": \"2015-05-05 06:28:18\",\n        \"updated_at\": \"2015-05-08 06:28:20\"\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Permission/PermissionController.php",
    "groupTitle": "Permission"
  },
  {
    "type": "post",
    "url": "/permission/update/:id",
    "title": "3.更新权限",
    "name": "update",
    "group": "Permission",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "inherit_id",
            "description": "<p>继承于.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>权限标题.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "slug",
            "description": "<p>操作路径(路由名).</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "descrition",
            "description": "<p>描述信息.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "note",
            "description": "<p>备注信息.</p> "
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": {\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"没有符合条件数据\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Permission/PermissionController.php",
    "groupTitle": "Permission"
  },
  {
    "type": "post",
    "url": "/role/create",
    "title": "4.创建角色",
    "name": "create",
    "group": "Role",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>必填,角色名称.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "department_id",
            "description": "<p>所属部门.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "city_id",
            "description": "<p>所属城市.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>角色状态.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "description",
            "description": "<p>角色状态.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "note",
            "description": "<p>备注信息.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Array</p> ",
            "optional": false,
            "field": "permission",
            "description": "<p>角色权限.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>请求状态.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "msg",
            "description": "<p>提示信息.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"角色更新失败\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Permission/RoleController.php",
    "groupTitle": "Role"
  },
  {
    "type": "post",
    "url": "/role/export",
    "title": "5.导出角色",
    "name": "export",
    "group": "Role",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "department_id",
            "description": "<p>可选,部门ID.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>可选,用户状态.1正常、2停用、3删除.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "city_id",
            "description": "<p>可选,城市ID.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "start",
            "description": "<p>可选,起始时间.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "end",
            "description": "<p>可选,结束时间.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "keyword",
            "description": "<p>可选,搜索关键字,匹配角色名.</p> "
          }
        ]
      }
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Permission/RoleController.php",
    "groupTitle": "Role"
  },
  {
    "type": "post",
    "url": "/role/index",
    "title": "1.角色列表",
    "name": "list",
    "group": "Role",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "department_id",
            "description": "<p>可选,部门ID.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>可选,用户状态.1正常、2停用、3删除.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "city_id",
            "description": "<p>可选,城市ID.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "start",
            "description": "<p>可选,起始时间.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "end",
            "description": "<p>可选,结束时间.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "keyword",
            "description": "<p>可选,搜索关键字,匹配角色名.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "page",
            "description": "<p>可选,页数.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "page_size",
            "description": "<p>可选,分页大小.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "total",
            "description": "<p>总数据量.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "per_page",
            "description": "<p>分页大小.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "current_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "last_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "from",
            "description": "<p>起始数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "to",
            "description": "<p>结束数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>角色名.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>用户状态.1正常、2停用、3删除.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "department_id",
            "description": "<p>所属部门.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "city_id",
            "description": "<p>所属城市.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "created_at",
            "description": "<p>创建时间.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "description",
            "description": "<p>角色描述.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "department",
            "description": "<p>角色职位.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "city",
            "description": "<p>角色区域.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": {\n        \"total\": 2,\n        \"per_page\": 20,\n        \"current_page\": 1,\n        \"last_page\": 1,\n        \"from\": 1,\n        \"to\": 2,\n        \"data\": [\n            {\n                \"name\": \"管理员\",\n                \"status\": \"1\",\n                \"created_at\": \"2015-05-05 06:23:43\",\n                \"description\": \"manage administration privileges\",\n                \"department\": {\n                    \"id\": 1,\n                    \"title\": \"产品部\"\n                },\n                \"city\": {\n                    \"id\": 1,\n                    \"title\": \"深圳\"\n                }\n            }\n        ]\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Permission/RoleController.php",
    "groupTitle": "Role"
  },
  {
    "type": "post",
    "url": "/role/show/:id",
    "title": "2.查看角色信息",
    "name": "show",
    "group": "Role",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "id",
            "description": "<p>必填,角色ID.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>角色名.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "slug",
            "description": "<p>保留字段.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "description",
            "description": "<p>描述信息.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "department_id",
            "description": "<p>所属部门.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "city_id",
            "description": "<p>所属城市.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>用户状态.1正常、2停用、3注销.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "note",
            "description": "<p>备注信息.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "created_at",
            "description": "<p>创建时间.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "update_at",
            "description": "<p>更新时间.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Array</p> ",
            "optional": false,
            "field": "permissions",
            "description": "<p>角色权限.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "department",
            "description": "<p>角色部门.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "city",
            "description": "<p>角色区域.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": {\n        \"id\": 1,\n        \"name\": \"管理员\",\n        \"slug\": \"administrator\",\n        \"description\": \"manage administration privileges\",\n        \"department_id\": 1,\n        \"city_id\": 1,\n        \"status\": \"1\",\n        \"note\": null,\n        \"created_at\": \"2015-05-05 06:23:43\",\n        \"updated_at\": \"2015-05-11 07:15:28\",\n        \"department\": {\n            \"id\": 1,\n            \"title\": \"产品部\"\n        },\n        \"city\": {\n            \"id\": 1,\n            \"title\": \"深圳\"\n        },\n        \"permissions\": [\n            {\n                \"id\": 3,\n                \"title\": \"修改用户信息\"\n            }\n        ]\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"没有符合条件数据\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Permission/RoleController.php",
    "groupTitle": "Role"
  },
  {
    "type": "post",
    "url": "/role/update/:id",
    "title": "3.更新角色信息",
    "name": "update",
    "group": "Role",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "id",
            "description": "<p>必填,用户ID.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>用户姓名.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "username",
            "description": "<p>用户名.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>用户状态.1正常、2停用、3注销.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "department_id",
            "description": "<p>所属部门.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "city_id",
            "description": "<p>所属城市.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Array</p> ",
            "optional": false,
            "field": "permissions",
            "description": "<p>用户角色.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>请求状态.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "msg",
            "description": "<p>提示信息.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"更新失败\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Permission/RoleController.php",
    "groupTitle": "Role"
  },
  {
    "type": "get",
    "url": "/shop_count/balance",
    "title": "9.商户往来列表",
    "name": "balance",
    "group": "ShopCount",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "key",
            "description": "<p>1 店铺搜索 2 商户搜索</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "keyword",
            "description": "<p>根据key来的关键字</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "page",
            "description": "<p>可选,页数. (从1开始)</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "size",
            "description": "<p>可选,分页大小.(最小1 最大500,默认10)</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "sort_key",
            "description": "<p>排序的键 ['id','created_at'(创建时间,默认),'salon_name','salon_type','pay_money','cost_money',...(money相关的key)]</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "sort_type",
            "description": "<p>排序的方式 1正序 2倒叙 (默认)</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "total",
            "description": "<p>总数据量.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "per_page",
            "description": "<p>分页大小.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "current_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "last_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "from",
            "description": "<p>起始数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "to",
            "description": "<p>结束数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "salon_name",
            "description": "<p>店铺名称.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "salon_type",
            "description": "<p>店铺名称类型(1预付款店 2投资店 3金字塔店).</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "pay_money",
            "description": "<p>预付款/付交易代收款.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "cost_money",
            "description": "<p>换算消费额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "spend_money",
            "description": "<p>交易消费额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "balance_money",
            "description": "<p>交易余额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "invest_money",
            "description": "<p>付投资款.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "invest_return_money",
            "description": "<p>付投款返还.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "invest_balance_money",
            "description": "<p>投资余额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "borrow_money",
            "description": "<p>付借款.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "borrow_return_money",
            "description": "<p>借款返还.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "borrow_balance_money",
            "description": "<p>借款余额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "merchant",
            "description": "<p>商盟信息.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": {\n        \"total\": 1,\n        \"per_page\": 10,\n        \"current_page\": 1,\n        \"last_page\": 1,\n        \"from\": 1,\n        \"to\": 1,\n        \"data\": [\n            {\n                \"id\": 1,\n                \"created_at\": \"2015-07-01 00:00:00\",\n                \"merchant_id\": 3,\n                \"salon_id\": 2,\n                \"salon_name\":\"米莱国际造型连锁(田贝店)\",\n                \"salon_type\":1,\n                \"pay_money\": \"123.00\",\n                \"cost_money\": \"111.00\",\n                \"spend_money\": \"23434.00\",\n                \"balance_money\": \"2334.00\",\n                \"invest_money\": \"2334.00\",\n                \"invest_return_money\": \"23.00\",\n                \"invest_balance_money\": \"343.00\",\n                \"borrow_money\": \"2323.00\",\n                \"borrow_return_money\": \"34.00\",\n                \"borrow_balance_money\": \"2334.00\",\n                \"merchant\": {\n                    \"id\": 3,\n                    \"name\": \"黎艳平\"\n                }\n            }\n        ]\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/ShopCount/ShopCountController.php",
    "groupTitle": "ShopCount"
  },
  {
    "type": "post",
    "url": "/shop_count/create",
    "title": "3.新建转付单",
    "name": "create",
    "group": "ShopCount",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "id",
            "description": "<p>有预览时  将预览生成的id带过来</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "merchant_id",
            "description": "<p>商户id 有id时可不填 否则为必填</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "salon_id",
            "description": "<p>店铺id 有id时可不填 否则为必填</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "pay_money",
            "description": "<p>付款金额  有id时可不填 否则为必填</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "cost_money",
            "description": "<p>换算消费额  有id时可不填 否则为必填</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "day",
            "description": "<p>付款日期 (YYYY-MM-DD) 有id时可不填 否则为必填</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "id",
            "description": "<p>创建成功后的id.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": {\n        \"id\": 1\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"参数有误,生成失败\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/ShopCount/ShopCountController.php",
    "groupTitle": "ShopCount"
  },
  {
    "type": "get",
    "url": "/shop_count/delegate_detail/{id}",
    "title": "8.代收单 详情",
    "name": "delegate_detail",
    "group": "ShopCount",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "id",
            "description": "<p>id</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "code",
            "description": "<p>代收单号.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "type",
            "description": "<p>代收类型.1项目消费.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "money",
            "description": "<p>代收金额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "created_at",
            "description": "<p>创建时间.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "day",
            "description": "<p>代收日期.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "salon",
            "description": "<p>店铺信息.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "merchant",
            "description": "<p>商盟信息.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": {\n        \"id\": 1,\n        \"created_at\": \"2015-07-02 00:00:00\",\n        \"merchant_id\": 2,\n        \"salon_id\": 3,\n        \"code\": \"dfasdagasdfasdfasd\",\n        \"type\": 1,\n        \"money\": \"3600.00\",\n        \"day\": \"2015-06-01\",\n        \"salon\": {\n            \"salonid\": 3,\n            \"salonname\": \"米莱国际造型连锁（田贝店）\"\n        },\n        \"merchant\": {\n            \"id\": 2,\n            \"name\": \"地对地导弹\"\n        }\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/ShopCount/ShopCountController.php",
    "groupTitle": "ShopCount"
  },
  {
    "type": "get",
    "url": "/shop_count/delegate_list",
    "title": "7.代收单 列表",
    "name": "delegate_list",
    "group": "ShopCount",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "key",
            "description": "<p>1 店铺搜索 2 商户搜索</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "keyword",
            "description": "<p>根据key来的关键字</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "pay_time_min",
            "description": "<p>付款最小时间 YYYY-MM-DD</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "pay_time_max",
            "description": "<p>付款最大时间 YYYY-MM-DD</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "page",
            "description": "<p>可选,页数. (从1开始)</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "size",
            "description": "<p>可选,分页大小.(最小1 最大500,默认10)</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "sort_key",
            "description": "<p>排序的键 ['id','created_at'(创建时间,默认),'code'(代收单号),'type'(代收类型),'money'(代收金额),'day'(代收日期)]</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "sort_type",
            "description": "<p>排序的方式 1正序 2倒叙 (默认)</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "total",
            "description": "<p>总数据量.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "per_page",
            "description": "<p>分页大小.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "current_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "last_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "from",
            "description": "<p>起始数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "to",
            "description": "<p>结束数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "code",
            "description": "<p>代收单号.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "type",
            "description": "<p>代收类型.1项目消费.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "money",
            "description": "<p>代收金额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "created_at",
            "description": "<p>创建时间.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "day",
            "description": "<p>代收日期.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "salon",
            "description": "<p>店铺信息.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "merchant",
            "description": "<p>商盟信息.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": {\n        \"total\": 1,\n        \"per_page\": 10,\n        \"current_page\": 1,\n        \"last_page\": 1,\n        \"from\": 1,\n        \"to\": 1,\n        \"data\": [\n            {\n                \"id\": 1,\n                \"created_at\": \"2015-07-02 00:00:00\",\n                \"merchant_id\": 2,\n                \"salon_id\": 3,\n                \"code\": \"dfasdagasdfasdfasd\",\n                \"type\": 1,\n                \"money\": \"3600.00\",\n                \"day\": \"2015-06-01\",\n                \"salon\": {\n                    \"salonid\": 3,\n                    \"salonname\": \"米莱国际造型连锁（田贝店）\"\n                },\n                \"merchant\": {\n                    \"id\": 2,\n                    \"name\": \"地对地导弹\"\n                }\n            }\n        ]\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/ShopCount/ShopCountController.php",
    "groupTitle": "ShopCount"
  },
  {
    "type": "post",
    "url": "/shop_count/destroy/{id}",
    "title": "6.转付单 删除",
    "name": "destroy",
    "group": "ShopCount",
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "ret",
            "description": "<p>1 成功删除</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": {\n        \"ret\": 1\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"参数有误,生成失败\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/ShopCount/ShopCountController.php",
    "groupTitle": "ShopCount"
  },
  {
    "type": "get",
    "url": "/shop_count/index",
    "title": "1.转付单列表",
    "name": "index",
    "group": "ShopCount",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "key",
            "description": "<p>1 店铺搜索 2 商户搜索</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "keyword",
            "description": "<p>根据key来的关键字</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "pay_time_min",
            "description": "<p>付款最小时间 YYYY-MM-DD</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "pay_time_max",
            "description": "<p>付款最大时间 YYYY-MM-DD</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "page",
            "description": "<p>可选,页数. (从1开始)</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "size",
            "description": "<p>可选,分页大小.(最小1 最大500,默认10)</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "sort_key",
            "description": "<p>排序的键 ['id','created_at'(创建时间,默认),'code'(付款单号),'type'(付款类型),'pay_money'(付款金额),'cost_money'(换算消费额),'day'(付款日期)]</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "sort_type",
            "description": "<p>排序的方式 1正序 2倒叙 (默认)</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "total",
            "description": "<p>总数据量.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "per_page",
            "description": "<p>分页大小.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "current_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "last_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "from",
            "description": "<p>起始数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "to",
            "description": "<p>结束数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "code",
            "description": "<p>付款单号.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "type",
            "description": "<p>付款单类型 1:预付交易款 2:付代收交易款</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "state",
            "description": "<p>状态 1:已付款 0:预览状态</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "pay_money",
            "description": "<p>付款金额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "cost_money",
            "description": "<p>换算消费额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "created_at",
            "description": "<p>创建时间.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "day",
            "description": "<p>付款日期.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "user",
            "description": "<p>制表人信息.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "salon",
            "description": "<p>店铺信息.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "merchant",
            "description": "<p>商盟信息.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": " {\n    \"result\": 1,\n    \"data\": {\n        \"total\": 1,\n        \"per_page\": 10,\n        \"current_page\": 1,\n        \"last_page\": 1,\n        \"from\": 1,\n        \"to\": 1,\n        \"data\": [\n            {\n               \"id\": 1,\n               \"created_at\": \"2015-07-03 00:00:00\",\n               \"merchant_id\": 1,\n               \"salon_id\": 1,\n               \"code\": \"fasdfasdfasdfasdfadfa\",\n               \"type\": 1,\n               \"uid\": 1,\n               \"pay_money\": \"2000.00\",\n               \"cost_money\": \"2500.00\",\n               \"day\": \"2015-07-02\",\n               \"user\": {\n                   \"id\": 1,\n                   \"name\": \"\"\n               },\n               \"salon\": {\n                   \"salonid\": 1,\n                   \"salonname\": \"嘉美专业烫染\"\n               },\n               \"merchant\": {\n                   \"id\": 1,\n                   \"name\": \"速度发多少\"\n               }\n            }\n        ]\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/ShopCount/ShopCountController.php",
    "groupTitle": "ShopCount"
  },
  {
    "type": "post",
    "url": "/shop_count/preview",
    "title": "2.转付单预览",
    "name": "preview",
    "group": "ShopCount",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "type",
            "description": "<p>转付单类型</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "merchant_id",
            "description": "<p>商户id</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "salon_id",
            "description": "<p>店铺id</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "pay_money",
            "description": "<p>付款金额</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "cost_money",
            "description": "<p>换算消费额</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "day",
            "description": "<p>付款日期 (YYYY-MM-DD)</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "code",
            "description": "<p>付款单号.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "type",
            "description": "<p>付款单类型 1:预付交易款 2:付代收交易款</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "state",
            "description": "<p>状态 1:已付款 0:预览状态</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "pay_money",
            "description": "<p>付款金额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "cost_money",
            "description": "<p>换算消费额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "created_at",
            "description": "<p>创建时间.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "day",
            "description": "<p>付款日期.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "user",
            "description": "<p>制表人信息.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "salon",
            "description": "<p>店铺信息.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "merchant",
            "description": "<p>商盟信息.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": {\n        \"id\": 1,\n        \"created_at\": \"2015-07-03 00:00:00\",\n        \"merchant_id\": 1,\n        \"salon_id\": 1,\n        \"code\": \"fasdfasdfasdfasdfadfa\",\n        \"type\": 1,\n        \"uid\": 1,\n        \"pay_money\": \"2000.00\",\n        \"cost_money\": \"2500.00\",\n        \"day\": \"2015-07-02\",\n        \"user\": {\n            \"id\": 1,\n            \"name\": \"\"\n        },\n        \"salon\": {\n            \"salonid\": 1,\n            \"salonname\": \"嘉美专业烫染\"\n        },\n        \"merchant\": {\n            \"id\": 1,\n            \"name\": \"速度发多少\"\n        }\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/ShopCount/ShopCountController.php",
    "groupTitle": "ShopCount"
  },
  {
    "type": "get",
    "url": "/shop_count/show/{id}",
    "title": "4.转付单详情",
    "name": "show",
    "group": "ShopCount",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "id",
            "description": "<p>id</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "code",
            "description": "<p>付款单号.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "type",
            "description": "<p>付款单类型 1:预付交易款 2:付代收交易款</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "state",
            "description": "<p>状态 1:已付款 0:预览状态</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "pay_money",
            "description": "<p>付款金额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "cost_money",
            "description": "<p>换算消费额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "created_at",
            "description": "<p>创建时间.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "day",
            "description": "<p>付款日期.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "user",
            "description": "<p>制表人信息.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "salon",
            "description": "<p>店铺信息.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "merchant",
            "description": "<p>商盟信息.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": {\n        \"id\": 1,\n        \"created_at\": \"2015-07-03 00:00:00\",\n        \"merchant_id\": 1,\n        \"salon_id\": 1,\n        \"code\": \"fasdfasdfasdfasdfadfa\",\n        \"type\": 1,\n        \"uid\": 1,\n        \"pay_money\": \"2000.00\",\n        \"cost_money\": \"2500.00\",\n        \"day\": \"2015-07-02\",\n        \"user\": {\n            \"id\": 1,\n            \"name\": \"\"\n        },\n        \"salon\": {\n            \"salonid\": 1,\n            \"salonname\": \"嘉美专业烫染\"\n        },\n        \"merchant\": {\n            \"id\": 1,\n            \"name\": \"速度发多少\"\n        }\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/ShopCount/ShopCountController.php",
    "groupTitle": "ShopCount"
  },
  {
    "type": "post",
    "url": "/shop_count/update/{id}",
    "title": "5.转付单 修改",
    "name": "update",
    "group": "ShopCount",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "merchant_id",
            "description": "<p>商户id</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "salon_id",
            "description": "<p>店铺id</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "pay_money",
            "description": "<p>付款金额</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "cost_money",
            "description": "<p>换算消费额</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "day",
            "description": "<p>付款日期 (YYYY-MM-DD)</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "id",
            "description": "<p>修改成功后的id.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": {\n        \"id\": 1\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"参数有误,生成失败\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/ShopCount/ShopCountController.php",
    "groupTitle": "ShopCount"
  },
  {
    "type": "post",
    "url": "/user/create",
    "title": "4.新增用户",
    "name": "create",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "username",
            "description": "<p>登录帐号.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>用户姓名.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "tel",
            "description": "<p>用户电话.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "department_id",
            "description": "<p>所属部门.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "position_id",
            "description": "<p>职位.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "city_id",
            "description": "<p>所属城市.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "email",
            "description": "<p>email.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>用户状态.1正常、2停用、3注销.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Array</p> ",
            "optional": false,
            "field": "roles",
            "description": "<p>用户角色.</p> "
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"用户创建失败\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/User/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "/user/export",
    "title": "5.导出用户",
    "name": "export",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "role_id",
            "description": "<p>可选,角色ID.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "department_id",
            "description": "<p>可选,部门ID.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>可选,用户状态.1正常、2停用、3注销.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "city_id",
            "description": "<p>可选,城市ID.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "start",
            "description": "<p>可选,起始时间.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "end",
            "description": "<p>可选,结束时间.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "keyword",
            "description": "<p>可选,搜索关键字,匹配帐号或者姓名.</p> "
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/User/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "/user/index",
    "title": "1.用户列表",
    "name": "list",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "role",
            "description": "<p>可选,角色名关键字.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>可选,姓名关键字.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "username",
            "description": "<p>可选,登录帐号关键字.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "department_id",
            "description": "<p>可选,部门ID.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>可选,用户状态.1正常、2停用、3注销.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "city_id",
            "description": "<p>可选,城市ID.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "start",
            "description": "<p>可选,起始时间.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "end",
            "description": "<p>可选,结束时间.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "page",
            "description": "<p>可选,页数.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "page_size",
            "description": "<p>可选,分页大小.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "sort_key",
            "description": "<p>排序的键,比如:created_at,update_at;</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "sort_type",
            "description": "<p>排序方式,DESC或者ASC;默认DESC</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "total",
            "description": "<p>总数据量.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "per_page",
            "description": "<p>分页大小.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "current_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "last_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "from",
            "description": "<p>起始数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "to",
            "description": "<p>结束数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>用户姓名.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "username",
            "description": "<p>用户名.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>用户状态.1正常、2停用、3注销.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Array</p> ",
            "optional": false,
            "field": "roles",
            "description": "<p>用户角色.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "department",
            "description": "<p>用户部门.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "city",
            "description": "<p>用户部门.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Object</p> ",
            "optional": false,
            "field": "position",
            "description": "<p>用户部门.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "created_at",
            "description": "<p>创建时间.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": {\n        \"total\": 3,\n        \"per_page\": 20,\n        \"current_page\": 1,\n        \"last_page\": 1,\n        \"from\": 1,\n        \"to\": 3,\n        \"data\": [\n            {\n                \"id\": 1,\n                \"username\": \"soothion\",\n                \"name\": \"老王\",\n                \"tel\": \"18617185201\",\n                \"email\": \"soothion@sina.com\",\n                \"result\": \"1\",\n                \"created_at\": \"2015-05-07 14:15:00\",\n                \"updated_at\": \"2015-05-11 07:18:23\",\n                \"roles\": [\n                    {\n                        \"role_id\": 2\n                    },\n                    {\n                        \"role_id\": 1\n                    },\n                \"department\": {\n\t                    \"id\": 1,\n\t                    \"title\": \"产品部\"\n\t                },\n                \"city\": {\n\t                    \"id\": 1,\n\t                    \"title\": \"深圳\"\n\t                },\n                \"position\": {\n\t                    \"id\": 1,\n\t                    \"title\": \"PHP\"\n\t                }\n                ]\n            }\n        ]\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/User/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "/user/show/:id",
    "title": "2.查看用户信息",
    "name": "show",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "id",
            "description": "<p>必填,用户ID.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "id",
            "description": "<p>用户ID.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "username",
            "description": "<p>用户名.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>用户姓名.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "tel",
            "description": "<p>用户电话.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "department_id",
            "description": "<p>所属部门.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "position_id",
            "description": "<p>职位.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "city_id",
            "description": "<p>所属城市.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "email",
            "description": "<p>email.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>用户状态.1正常、2停用、3注销.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "created_at",
            "description": "<p>创建时间.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "update_at",
            "description": "<p>更新时间.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Array</p> ",
            "optional": false,
            "field": "roles",
            "description": "<p>用户角色.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": {\n        \"id\": 1,\n        \"username\": \"soothion\",\n        \"name\": \"老王\",\n        \"tel\": \"18617185201\",\n        \"department_id\": 1,\n        \"position_id\": 1,\n        \"city_id\": 1,\n        \"email\": \"soothion@sina.com\",\n        \"result\": \"1\",\n        \"created_at\": \"2015-05-07 14:15:00\",\n        \"updated_at\": \"2015-05-11 07:18:23\",\n        \"roles\": [\n            {\n                \"id\": 1,\n                \"name\": \"test1sssssssssss\",\n                \"slug\": \"administrator\",\n                \"description\": \"manage administration privileges\",\n                \"department_id\": 1,\n                \"city_id\": 1,\n                \"result\": \"1\",\n                \"note\": null,\n                \"created_at\": \"2015-05-05 06:23:43\",\n                \"updated_at\": \"2015-05-11 07:15:28\"\n            }\n        ]\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/User/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "/user/update/:id",
    "title": "3.更新用户信息",
    "name": "update",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>用户姓名.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "old_password",
            "description": "<p>用户原密码.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "password",
            "description": "<p>用户新密码.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "tel",
            "description": "<p>用户电话.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "department_id",
            "description": "<p>所属部门.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "position_id",
            "description": "<p>职位.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "city_id",
            "description": "<p>所属城市.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "email",
            "description": "<p>email.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>用户状态.1正常、2停用、3注销.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Array</p> ",
            "optional": false,
            "field": "roles",
            "description": "<p>用户角色.</p> "
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"没有符合条件数据\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/User/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "/merchant/checkMerchantSn",
    "title": "5.检测商家编号是否重复",
    "name": "checkMerchantSn",
    "group": "merchant",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "sn",
            "description": "<p>必填商家编号.</p> "
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"msg\": \"\",\n    \"data\": {\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"店铺编号重复已经存在\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/MerchantController.php",
    "groupTitle": "merchant"
  },
  {
    "type": "post",
    "url": "/merchant/del",
    "title": "4.删除商户",
    "name": "del",
    "group": "merchant",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "id",
            "description": "<p>删除必填,商家ID.</p> "
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"msg\": \"\",\n    \"data\": {\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"商户删除失败\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/MerchantController.php",
    "groupTitle": "merchant"
  },
  {
    "type": "post",
    "url": "/merchant/getMerchantList",
    "title": "6.获取商户详情",
    "name": "getMerchantList",
    "group": "merchant",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "id",
            "description": "<p>必填商家id.</p> "
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": {\n        \"id\": 48,\n        \"sn\": \"00048\",\n        \"name\": \"sn手动输入\",\n        \"contact\": \"汪先生\",\n        \"mobile\": \"13458745236\",\n        \"phone\": \"0755236566\",\n        \"email\": \"\",\n        \"addr\": \"\",\n        \"foundingDate\": 0,\n        \"addTime\": 1432202115,\n        \"upTime\": 0,\n        \"status\": 1,\n        \"salonNum\": 0\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"参数错误\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/MerchantController.php",
    "groupTitle": "merchant"
  },
  {
    "type": "post",
    "url": "/merchant/index",
    "title": "1.商户列表",
    "name": "index",
    "group": "merchant",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "mobile",
            "description": "<p>可选,电话号码</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>可选,商户名</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "page",
            "description": "<p>可选,页数.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "page_size",
            "description": "<p>可选,分页大小.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "total",
            "description": "<p>总数据量.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "per_page",
            "description": "<p>分页大小.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "current_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "last_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "from",
            "description": "<p>起始数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "to",
            "description": "<p>结束数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "id",
            "description": "<p>id.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "sn",
            "description": "<p>商户编号.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>用户姓名.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "contact",
            "description": "<p>联系人.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "mobile",
            "description": "<p>用户姓名.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "phone",
            "description": "<p>电话.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "email",
            "description": "<p>邮箱.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "addr",
            "description": "<p>地址.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "foundingDate",
            "description": "<p>商户成立时间(时间戳).</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "salonNum",
            "description": "<p>拥有店铺数量.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": {\n        \"total\": 51,\n        \"per_page\": \"1\",\n        \"current_page\": 1,\n        \"last_page\": 51,\n        \"from\": 1,\n        \"to\": 1,\n        \"data\": [\n            {\n                \"id\": 53,\n                \"sn\": \"0000900\",\n                \"name\": \"s卡段商户\",\n                \"contact\": \"汪先生\",\n                \"mobile\": \"13458745236\",\n                \"phone\": \"0755236566\",\n                \"email\": \"\",\n                \"addr\": \"\",\n                \"foundingDate\": 1432202590,\n                \"salonNum\": 0,\n                \"addTime\": 1432202951\n            }\n        ]\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/MerchantController.php",
    "groupTitle": "merchant"
  },
  {
    "type": "post",
    "url": "/merchant/save",
    "title": "2.添加商户",
    "name": "save",
    "group": "merchant",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "sn",
            "description": "<p>必填,商户编号.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>必填,用户姓名.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "contact",
            "description": "<p>必填,联系人.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "mobile",
            "description": "<p>必填,用户姓名.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "phone",
            "description": "<p>电话.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "email",
            "description": "<p>邮箱.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "addr",
            "description": "<p>地址.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "foundingDate",
            "description": "<p>商户成立时间.</p> "
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"msg\": \"\",\n    \"data\": {\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/MerchantController.php",
    "groupTitle": "merchant"
  },
  {
    "type": "post",
    "url": "/merchant/update",
    "title": "3.修改商户",
    "name": "update",
    "group": "merchant",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "id",
            "description": "<p>必填,商家Id.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "sn",
            "description": "<p>必填,商户编号.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>必填,用户姓名.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "contact",
            "description": "<p>必填,联系人.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "mobile",
            "description": "<p>必填,用户姓名.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "phone",
            "description": "<p>电话.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "email",
            "description": "<p>邮箱.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "addr",
            "description": "<p>地址.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "foundingDate",
            "description": "<p>商户成立时间.</p> "
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"msg\": \"\",\n    \"data\": {\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/MerchantController.php",
    "groupTitle": "merchant"
  },
  {
    "type": "post",
    "url": "/salonAccount/delAct",
    "title": "4.停用 删除账号",
    "name": "delAct",
    "group": "salonAccount",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "salonUserId",
            "description": "<p>必填,账号ID.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "type",
            "description": "<p>必填,操作类型 1.停用 2.删除.</p> "
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"msg\": \"\",\n    \"data\": {\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"操作失败\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/SalonAccountController.php",
    "groupTitle": "salonAccount"
  },
  {
    "type": "post",
    "url": "/salonAccount/getSalonName",
    "title": "5.模糊查找店铺",
    "name": "getSalonName",
    "group": "salonAccount",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "salonname",
            "description": "<p>必填,店铺名称.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "merchantId",
            "description": "<p>商户Id.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>商户名称.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "salonid",
            "description": "<p>店铺Id.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "salonname",
            "description": "<p>店铺名称.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\t{\n\t    \"result\": 1,\n\t    \"data\": [\n\t        {\n\t            \"merchantId\": 1,\n\t            \"salonid\": 1,\n\t            \"salonname\": \"嘉美专业烫染\",\n\t\t\t\t\"name\": \"嘉烫染\"\n\t        },\n\t        {\n\t            \"merchantId\": 33,\n\t            \"salonid\": 804,\n\t            \"salonname\": \"臭美腾讯专属高端店\"\n             \"name\": \"嘉美烫染\"\n\t        },\n\t        ......\n\t    ]\n\t}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"操作失败\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/SalonAccountController.php",
    "groupTitle": "salonAccount"
  },
  {
    "type": "post",
    "url": "/salonAccount/index",
    "title": "1.店铺账号列表",
    "name": "index",
    "group": "salonAccount",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "salonname",
            "description": "<p>可选,店铺名称</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>可选,商户名</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "username",
            "description": "<p>可选,账号名称</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "page",
            "description": "<p>可选,页数.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "page_size",
            "description": "<p>可选,分页大小.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "sort_key",
            "description": "<p>可选,排序字段 status状态 roleType角色.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "sort_type",
            "description": "<p>可选,排序 DESC倒序 ASC升序.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "total",
            "description": "<p>总数据量.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "per_page",
            "description": "<p>分页大小.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "current_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "last_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "from",
            "description": "<p>起始数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "to",
            "description": "<p>结束数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "salonUserId",
            "description": "<p>账号Id.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "username",
            "description": "<p>账号名称.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "salonid",
            "description": "<p>店铺Id.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "roleType",
            "description": "<p>账号类型 1.普通用户2.超级管理员.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "addTime",
            "description": "<p>创建时间 （时间戳 1436242693）.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "status",
            "description": "<p>状态  1.正常使用2.已停用3.已删除.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "merchantId",
            "description": "<p>商户Id.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>商户名称.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "salonname",
            "description": "<p>店铺名称.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": " {\n\t    \"result\": 1,\n\t    \"data\": {\n\t        \"total\": 3,\n\t        \"per_page\": 20,\n\t        \"current_page\": 1,\n\t        \"last_page\": 1,\n\t        \"from\": 1,\n\t        \"to\": 3,\n\t        \"data\": [\n\t            {\n\t                \"salonUserId\": 1155,\n\t                \"username\": \"臭美商盟美发店\",\n\t                \"salonid\": 2,\n\t                \"roleType\": 1,\n\t                \"addTime\": 1436236918,\n\t                \"status\": 3,\n\t                \"merchantId\": 1,\n\t                \"name\": \"15854856985\",\n\t                \"salonname\": \"名流造型SPA（皇岗店）\"\n\t            },\n\t            ......\n\t        ]\n\t    }\n\t}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/SalonAccountController.php",
    "groupTitle": "salonAccount"
  },
  {
    "type": "post",
    "url": "/salonAccount/resetPwd",
    "title": "3.重置密码",
    "name": "resetPwd",
    "group": "salonAccount",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "salonUserId",
            "description": "<p>必填,账号ID.</p> "
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"msg\": \"\",\n    \"data\": {\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"操作失败\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/SalonAccountController.php",
    "groupTitle": "salonAccount"
  },
  {
    "type": "post",
    "url": "/salonAccount/save",
    "title": "2.添加账号",
    "name": "save",
    "group": "salonAccount",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "username",
            "description": "<p>必填,用户名.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "salonid",
            "description": "<p>必填,店铺Id.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "merchantId",
            "description": "<p>必填,商户Id.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "roleType",
            "description": "<p>必填,账号类型 1.普通用户2.超级管理员.</p> "
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"msg\": \"\",\n    \"data\": {\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"当前店铺已存在普通用户，请查询\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/SalonAccountController.php",
    "groupTitle": "salonAccount"
  },
  {
    "type": "post",
    "url": "/salonList/getBussesName",
    "title": "2.获取所有业务代表",
    "name": "getBussesName",
    "group": "salonList",
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{   \n    \"result\": 1,//省\n    \"data\": [\n        {\n            \"id\": 1,\n            \"businessName\": \"张三\"\n        },\n        {\n            \"id\": 2,\n            \"businessName\": \"李四\"\n        },\n        ......\n    ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/ListController.php",
    "groupTitle": "salonList"
  },
  {
    "type": "post",
    "url": "/salonList/getProvinces",
    "title": "1.获取省市区商圈菜单",
    "name": "getProvinces",
    "group": "salonList",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "type",
            "description": "<p>获取类型  1 省 2市 3区 4商圈.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "areaId",
            "description": "<p>上级Id(获取下级时必填，上级Id).</p> "
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{   \n    \"result\": 1,//省\n    \"data\": [\n        {\n            \"pid\": 1,\n            \"pname\": \"广东省\"\n        },\n        {\n            \"pid\": 2,\n            \"pname\": \"北京市\"\n        },\n        ......\n    ]\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{   \n    \"result\": 1,//市\n    \"data\": [\n        {\n            \"iid\": 1,\n            \"iname\": \"深圳市\"\n        },\n        {\n            \"iid\": 2,\n            \"iname\": \"广州市\"\n        },\n        ......\n    ]\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{   \n    \"result\": 1,//区\n    \"data\": [\n        {\n            \"tid\": 1,\n            \"tname\": \"福田区\"\n        },\n        {\n            \"tid\": 2,\n            \"tname\": \"罗湖区\"\n        },\n        ......\n    ]\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{   \n    \"result\": 1,//商圈\n    \"data\": [\n        {\n            \"areaid\": 16,\n            \"areaname\": \"香蜜湖\"\n        },\n        {\n            \"areaid\": 21,\n            \"areaname\": \"八卦路\"\n        },\n        ......\n    ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/ListController.php",
    "groupTitle": "salonList"
  },
  {
    "type": "post",
    "url": "/salon/checkSalonSn",
    "title": "5.检测店铺编号是否重复",
    "name": "checkSalonSn",
    "group": "salon",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "sn",
            "description": "<p>必填店铺编号.</p> "
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"msg\": \"\",\n    \"data\": {\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"店铺编号重复已经存在\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/SalonController.php",
    "groupTitle": "salon"
  },
  {
    "type": "post",
    "url": "/salon/del",
    "title": "7.删除店铺",
    "name": "del",
    "group": "salon",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "salonid",
            "description": "<p>删除必填,店铺Id.</p> "
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"msg\": \"\",\n    \"data\": {\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"删除失败\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/SalonController.php",
    "groupTitle": "salon"
  },
  {
    "type": "post",
    "url": "/salon/endCooperation",
    "title": "6.终止合作",
    "name": "endCooperation",
    "group": "salon",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "salonid",
            "description": "<p>必填,店铺ID.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "type",
            "description": "<p>必填,操作类型 1终止合作 2恢复店铺.</p> "
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"msg\": \"\",\n    \"data\": {\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"操作失败请重新再试\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/SalonController.php",
    "groupTitle": "salon"
  },
  {
    "type": "post",
    "url": "/salon/getSalon",
    "title": "4.获取店铺详情",
    "name": "getSalon",
    "group": "salon",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "salonid",
            "description": "<p>必填,店铺id.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "sn",
            "description": "<p>店铺编号.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "salestatus",
            "description": "<p>状态 0终止合作 1正常合作.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "salonname",
            "description": "<p>店铺名.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "district",
            "description": "<p>行政地区 .</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "addr",
            "description": "<p>详细街道信息.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "addrlati",
            "description": "<p>地理坐标纬度.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "addrlong",
            "description": "<p>地理坐标经度.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "zone",
            "description": "<p>所属商圈 .</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "shopType",
            "description": "<p>店铺类型  1预付款店 2投资店 3金字塔店.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "contractTime",
            "description": "<p>合同日期   时间戳.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "contractPeriod",
            "description": "<p>合同期限 y_m.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "bargainno",
            "description": "<p>合同编号.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "bcontacts",
            "description": "<p>联系人.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "tel",
            "description": "<p>联系电话.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "phone",
            "description": "<p>店铺座机.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "corporateName",
            "description": "<p>法人代表.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "corporateTel",
            "description": "<p>法人电话.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "businessId",
            "description": "<p>业务代表ID.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "bankName",
            "description": "<p>银行名称.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "beneficiary",
            "description": "<p>收款人.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "bankCard",
            "description": "<p>银行卡号.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "branchName",
            "description": "<p>支行名称.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "accountType",
            "description": "<p>帐户类型 1. 对公帐户 2.对私帐户.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "salonArea",
            "description": "<p>店铺面积.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "dressingNums",
            "description": "<p>镜台数量.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "staffNums",
            "description": "<p>员工总人数.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "stylistNums",
            "description": "<p>发型师人数.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "monthlySales",
            "description": "<p>店铺平均月销售额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "totalSales",
            "description": "<p>年销售总额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "price",
            "description": "<p>本店客单价.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "payScale",
            "description": "<p>充值卡占月销售额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "payMoney",
            "description": "<p>销售最多的充值卡金额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "payMoneyScale",
            "description": "<p>销售最多的充值卡折扣.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "payCountScale",
            "description": "<p>占全部充值总额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "cashScale",
            "description": "<p>每月非充值卡现金占销售额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "blowScale",
            "description": "<p>洗剪吹占销售额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "hdScale",
            "description": "<p>烫染占销售额.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "platformName",
            "description": "<p>O2O平台合作.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "platformScale",
            "description": "<p>合作O2O销售额占比.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "receptionNums",
            "description": "<p>本店正常工作时间每日最多可接待人次理论数.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "receptionMons",
            "description": "<p>均实际每月接待.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "setupTime",
            "description": "<p>店铺成立时间 (时间戳).</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "hotdyeScale",
            "description": "<p>店铺租金.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "lastValidity",
            "description": "<p>店铺租赁合同剩余有效期.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "salonType",
            "description": "<p>店铺类型 1纯社区店 2社区商圈店 3商圈店 4商场店 5工作室（写字楼)）,多选  1_3  下划线拼接.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "contractPicUrl",
            "description": "<p>合同图片 json数组.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "licensePicUrl",
            "description": "<p>营业执照 json数组.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "corporatePicUrl",
            "description": "<p>法人执照 json数组.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "zoneName",
            "description": "<p>商圈名.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "districtName",
            "description": "<p>区域名称.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "citiesName",
            "description": "<p>市名称.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "citiesId",
            "description": "<p>市Id.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "provinceName",
            "description": "<p>省名称.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "provinceId",
            "description": "<p>省Id</p> "
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/SalonController.php",
    "groupTitle": "salon"
  },
  {
    "type": "post",
    "url": "/salon/index",
    "title": "1.店铺列表",
    "name": "index",
    "group": "salon",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "shopType",
            "description": "<p>可选,店铺类型</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "district",
            "description": "<p>可选,区域</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "zone",
            "description": "<p>可选,所属商圈</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "salonname",
            "description": "<p>可选,店名</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "businessName",
            "description": "<p>可选,业务代表</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "sort_key",
            "description": "<p>可选,排序字段 shopType 店铺类型  salestatus 状态.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "sort_type",
            "description": "<p>可选,排序 DESC倒序 ASC升序.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "page",
            "description": "<p>可选,页数.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "page_size",
            "description": "<p>可选,分页大小.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "total",
            "description": "<p>总数据量.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "per_page",
            "description": "<p>分页大小.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "current_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "last_page",
            "description": "<p>当前页面.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "from",
            "description": "<p>起始数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "to",
            "description": "<p>结束数据.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "salonid",
            "description": "<p>店铺Id.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "salonname",
            "description": "<p>店铺名称.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "shopType",
            "description": "<p>店铺类型 店铺类型  1预付款店 2投资店 3金字塔店.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "zone",
            "description": "<p>商圈.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "district",
            "description": "<p>行政区域.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "salestatus",
            "description": "<p>状态 0终止合作 1正常合作.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "businessId",
            "description": "<p>业务ID.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "sn",
            "description": "<p>地址.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "add_time",
            "description": "<p>添加时间(10位时间戳).</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>商户名.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "merchantId",
            "description": "<p>商户ID.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "businessName",
            "description": "<p>业务代表名.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "zoneName",
            "description": "<p>商圈名.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "districtName",
            "description": "<p>区域名称.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "citiesName",
            "description": "<p>市名称.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "citiesId",
            "description": "<p>市Id.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "provinceName",
            "description": "<p>省名称.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "provinceId",
            "description": "<p>省Id</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"data\": {\n        \"total\": 782,\n        \"per_page\": \"1\",\n        \"current_page\": 1,\n        \"last_page\": 782,\n        \"from\": 1,\n        \"to\": 1,\n        \"data\": [\n            {\n                \"salonid\": 796,\n                \"salonname\": \"亮丽人生\",\n                \"shopType\": 3,\n                \"zone\": 0,\n                \"district\": 0,\n                \"salestatus\": 1,\n                \"businessId\": 4,\n                \"sn\": \"0002701\",\n                \"add_time\": 1432017651,\n                \"name\": \"美好年代1\",\n                \"merchantId\": 27,\n                \"businessName\": \"\",\n                \"zoneName\": \"\",\n                \"districtName\": \"\",\n                \"citiesName\": \"\",\n                \"citiesId\": \"\",\n                \"provinceName\": \"\",\n                \"provinceId\": \"\"\n            }\n        ]\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"未授权访问\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/SalonController.php",
    "groupTitle": "salon"
  },
  {
    "type": "post",
    "url": "/salon/save",
    "title": "2.店铺添加",
    "name": "save",
    "group": "salon",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "merchantId",
            "description": "<p>必填,商户Id.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "sn",
            "description": "<p>必填,店铺编号.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "salonname",
            "description": "<p>必填,店名.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "district",
            "description": "<p>必填,行政地区 .</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "addr",
            "description": "<p>必填,详细街道信息.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "addrlati",
            "description": "<p>必填,地理坐标纬度.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "addrlong",
            "description": "<p>必填,地理坐标经度.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "zone",
            "description": "<p>必填,所属商圈 .</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "shopType",
            "description": "<p>必填,店铺类型  1预付款店 2投资店 3金字塔店.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "contractTime",
            "description": "<p>可选,合同日期  Y-m-d.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "contractPeriod",
            "description": "<p>可选,合同期限 y_m.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "bargainno",
            "description": "<p>可选,合同编号.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "bcontacts",
            "description": "<p>可选,联系人.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "tel",
            "description": "<p>必填,联系电话.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "phone",
            "description": "<p>必填,店铺座机.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "corporateName",
            "description": "<p>必填,法人代表.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "corporateTel",
            "description": "<p>必填,法人电话.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "businessId",
            "description": "<p>必填,业务代表Id.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "bankName",
            "description": "<p>必填,银行名称.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "beneficiary",
            "description": "<p>必填,收款人.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "bankCard",
            "description": "<p>必填,银行卡号.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "branchName",
            "description": "<p>必填,支行名称.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "accountType",
            "description": "<p>必填,帐户类型 1. 对公帐户 2.对私帐户.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "salonArea",
            "description": "<p>可选,店铺面积.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "dressingNums",
            "description": "<p>可选,镜台数量.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "staffNums",
            "description": "<p>可选,员工总人数.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "stylistNums",
            "description": "<p>可选,发型师人数.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "monthlySales",
            "description": "<p>可选,店铺平均月销售额.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "totalSales",
            "description": "<p>可选,年销售总额.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "price",
            "description": "<p>可选,本店客单价.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "payScale",
            "description": "<p>可选,充值卡占月销售额.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "payMoney",
            "description": "<p>可选,销售最多的充值卡金额.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "payMoneyScale",
            "description": "<p>可选,销售最多的充值卡折扣.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "payCountScale",
            "description": "<p>可选,占全部充值总额.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "cashScale",
            "description": "<p>可选,每月非充值卡现金占销售额.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "blowScale",
            "description": "<p>可选,洗剪吹占销售额.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "hdScale",
            "description": "<p>可选,烫染占销售额.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "platformName",
            "description": "<p>可选,O2O平台合作.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "platformScale",
            "description": "<p>可选,合作O2O销售额占比.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "receptionNums",
            "description": "<p>可选,本店正常工作时间每日最多可接待人次理论数.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "receptionMons",
            "description": "<p>可选,均实际每月接待.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "setupTime",
            "description": "<p>可选,店铺成立时间 Y-m-d.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "hotdyeScale",
            "description": "<p>可选,店铺租金.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "lastValidity",
            "description": "<p>可选,店铺租赁合同剩余有效期.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "salonType",
            "description": "<p>可选,店铺类型 1纯社区店 2社区商圈店 3商圈店 4商场店 5工作室（写字楼)）,多选  1_3  下划线拼接.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "contractPicUrl",
            "description": "<p>可选,合同图片 json数组.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "licensePicUrl",
            "description": "<p>可选,营业执照 json数组.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "corporatePicUrl",
            "description": "<p>可选,法人执照 json数组.</p> "
          }
        ]
      }
    },
    "description": "<p>合同图片 营业执照 法人执照 demo [ { &quot;img&quot;: &quot;http://choumei2.test.com/merchant/index.jpg&quot;,    //大图 &quot;thumbimg&quot;: &quot;http://choumei2.test.com/sindex.jpg&quot;       //缩略图 }, { &quot;img&quot;: &quot;http://choumei2.test.com/merchant/index.jpg&quot;, &quot;thumbimg&quot;: &quot;http://choumei2.test.com/sindex.jpg&quot; }, { &quot;img&quot;: &quot;http://choumei2.test.com/merchant/index.jpg&quot;, &quot;thumbimg&quot;: &quot;http://choumei2.test.com/sindex.jpg&quot; } ]</p> ",
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"msg\": \"\",\n    \"data\": {\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"店铺更新失败\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/SalonController.php",
    "groupTitle": "salon"
  },
  {
    "type": "post",
    "url": "/salon/update",
    "title": "3.店铺更新",
    "name": "update",
    "group": "salon",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "merchantId",
            "description": "<p>必填,商户Id</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "salonid",
            "description": "<p>必填,店铺id .</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "sn",
            "description": "<p>必填,店铺编号.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "salonname",
            "description": "<p>必填,店名.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "district",
            "description": "<p>必填,行政地区 .</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "addr",
            "description": "<p>必填,详细街道信息.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "addrlati",
            "description": "<p>必填,地理坐标纬度.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "addrlong",
            "description": "<p>必填,地理坐标经度.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "zone",
            "description": "<p>必填,所属商圈 .</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "shopType",
            "description": "<p>必填,店铺类型  1预付款店 2投资店 3金字塔店.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "contractTime",
            "description": "<p>可选,合同日期  Y-m-d.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "contractPeriod",
            "description": "<p>可选,合同期限 y_m.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "bargainno",
            "description": "<p>可选,合同编号.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "bcontacts",
            "description": "<p>可选,联系人.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "tel",
            "description": "<p>必填,联系电话.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "phone",
            "description": "<p>必填,店铺座机.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "corporateName",
            "description": "<p>必填,法人代表.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "corporateTel",
            "description": "<p>必填,法人电话.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "businessId",
            "description": "<p>必填,业务代表Id.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "bankName",
            "description": "<p>必填,银行名称.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "beneficiary",
            "description": "<p>必填,收款人.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "bankCard",
            "description": "<p>必填,银行卡号.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "branchName",
            "description": "<p>必填,支行名称.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "accountType",
            "description": "<p>必填,帐户类型 1. 对公帐户 2.对私帐户.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "salonArea",
            "description": "<p>可选,店铺面积.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "dressingNums",
            "description": "<p>可选,镜台数量.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "staffNums",
            "description": "<p>可选,员工总人数.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "stylistNums",
            "description": "<p>可选,发型师人数.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "monthlySales",
            "description": "<p>可选,店铺平均月销售额.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "totalSales",
            "description": "<p>可选,年销售总额.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "price",
            "description": "<p>可选,本店客单价.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "payScale",
            "description": "<p>可选,充值卡占月销售额.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "payMoney",
            "description": "<p>可选,销售最多的充值卡金额.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "payMoneyScale",
            "description": "<p>可选,销售最多的充值卡折扣.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "payCountScale",
            "description": "<p>可选,占全部充值总额.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "cashScale",
            "description": "<p>可选,每月非充值卡现金占销售额.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "blowScale",
            "description": "<p>可选,洗剪吹占销售额.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "hdScale",
            "description": "<p>可选,烫染占销售额.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "platformName",
            "description": "<p>可选,O2O平台合作.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "platformScale",
            "description": "<p>可选,合作O2O销售额占比.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "receptionNums",
            "description": "<p>可选,本店正常工作时间每日最多可接待人次理论数.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "receptionMons",
            "description": "<p>可选,均实际每月接待.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "setupTime",
            "description": "<p>可选,店铺成立时间 Y-m-d.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "hotdyeScale",
            "description": "<p>可选,店铺租金.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "lastValidity",
            "description": "<p>可选,店铺租赁合同剩余有效期.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "salonType",
            "description": "<p>可选,店铺类型 1纯社区店 2社区商圈店 3商圈店 4商场店 5工作室（写字楼)）,多选  1_3  下划线拼接.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "contractPicUrl",
            "description": "<p>可选,合同图片 json数组.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "licensePicUrl",
            "description": "<p>可选,营业执照 json数组.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "corporatePicUrl",
            "description": "<p>可选,法人执照 json数组.</p> "
          }
        ]
      }
    },
    "description": "<p>合同图片 营业执照 法人执照 demo [ { &quot;img&quot;: &quot;http://choumei2.test.com/merchant/index.jpg&quot;,    //大图 &quot;thumbimg&quot;: &quot;http://choumei2.test.com/sindex.jpg&quot;       //缩略图 }, { &quot;img&quot;: &quot;http://choumei2.test.com/merchant/index.jpg&quot;, &quot;thumbimg&quot;: &quot;http://choumei2.test.com/sindex.jpg&quot; }, { &quot;img&quot;: &quot;http://choumei2.test.com/merchant/index.jpg&quot;, &quot;thumbimg&quot;: &quot;http://choumei2.test.com/sindex.jpg&quot; } ]</p> ",
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"result\": 1,\n    \"msg\": \"\",\n    \"data\": {\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n    \"result\": 0,\n    \"msg\": \"店铺更新失败\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Merchant/SalonController.php",
    "groupTitle": "salon"
  }
] });
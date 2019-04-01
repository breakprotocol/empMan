myApp.controller('CategoryCtrl', function ($scope, TemplateService, NavigationService, $timeout, $stateParams, $state, toastr, $uibModal) {
  //Used to name the .html file
  $scope.template = TemplateService.changecontent("category");
  $scope.menutitle = NavigationService.makeactive("Category");
  TemplateService.title = $scope.menutitle;
  $scope.navigation = NavigationService.getnav();

  $scope.formData = {};
  $scope.formData.page = 1;
  $scope.formData.type = '';
  $scope.formData.keyword = '';

  $scope.searchInTable = function (data) {
    $scope.formData.page = 1;
    if (data.length >= 2) {
      $scope.formData.keyword = data;
      $scope.viewTable();
    } else if (data.length == '') {
      $scope.formData.keyword = data;
      $scope.viewTable();
    }
  }

  $scope.viewTable = function () {
    $scope.formData.page = $scope.formData.page++;
    NavigationService.getAll("/categories/getAll", $scope.formData, function (data) {
      $scope.items = data.result;
      $scope.totalItems = data.totalCount;
    });
  }
  $scope.viewTable();

  // DELETE
  $scope.confDel = function (data) {
    $scope.deleteId = data;
    $scope.modalInstance = $uibModal.open({
      animation: $scope.animationsEnabled,
      templateUrl: 'views/modal/delete.html',
      backdrop: 'static',
      keyboard: false,
      size: 'sm',
      scope: $scope
    });
  };

  $scope.noDelete = function () {
    $scope.modalInstance.close();
  }

  $scope.delete = function (id) {
    // console.log(data);
    var url = "/categories/delete";
    var obj = {};
    obj.id = id;
    NavigationService.delete(url, obj, function (data) {
      if (data == '1' || data == "true") {
        toastr.success('Successfully Deleted', 'Sponsor Page');
        $scope.modalInstance.close();
        $scope.viewTable();
      } else {
        toastr.error('Something Went Wrong while Deleting', 'Sponsor Page');
      }
    });
  }
  // DELETE END
});

myApp.controller('DetailCategoryCtrl', function ($scope, TemplateService, NavigationService, $timeout, $stateParams, $state, toastr) {
  //Used to name the .html file
  $scope.template = TemplateService.changecontent("detailcategory");
  $scope.menutitle = NavigationService.makeactive("Special Award");
  TemplateService.title = $scope.menutitle;
  $scope.navigation = NavigationService.getnav();
  $scope.title = 'Create';
  $scope.formData = {};
  if ($stateParams.id) {
    $scope.title = "Edit";
    NavigationService.getOneCategory($stateParams.id, function (data) {
      console.log("data", data);
      $scope.formData = {
        "_id": data[0]._id,
        "name": data[0].name,
        "priority": data[0].priority
      }
    });

  }


  // SAVE
  $scope.saveData = function (data) {
    var url = "";
    if ($stateParams.id) {
      //edit
      url = adminurl + "/categories/update"
    } else {
      //create
      url = adminurl + "/categories/create"
    }


    NavigationService.saveCategory(url, data, function (data) {
      if (data == '1' || data == 'true') {
        toastr.success("Data saved successfully", 'Success');
        $scope.formData._id = "";
        $scope.formData.name = "";
        $scope.formData.priority = "";
      } else {

      }
    });
  }
  // SAVE END

  $scope.onCancel = function (sendTo) {

    $state.go(sendTo);
  };
});

myApp.controller('SubCategoryCtrl', function ($scope, TemplateService, NavigationService, $timeout, $stateParams, $state, toastr, $uibModal) {
  //Used to name the .html file
  $scope.template = TemplateService.changecontent("subcategory");
  $scope.menutitle = NavigationService.makeactive("Sub Category");
  TemplateService.title = $scope.menutitle;
  $scope.navigation = NavigationService.getnav();

  $scope.formData = {};
  $scope.formData.page = 1;
  $scope.formData.type = '';
  $scope.formData.keyword = '';

  $scope.searchInTable = function (data) {
    $scope.formData.page = 1;
    if (data.length >= 2) {
      $scope.formData.keyword = data;
      $scope.viewTable();
    } else if (data.length == '') {
      $scope.formData.keyword = data;
      $scope.viewTable();
    }
  }

  $scope.viewTable = function () {
    $scope.formData.page = $scope.formData.page++;
    NavigationService.getAll("/sub_categories/getAll", $scope.formData, function (data) {
      $scope.items = data.result;
      $scope.totalItems = data.totalCount;
    });
  }
  $scope.viewTable();

  // DELETE
  $scope.confDel = function (data) {
    $scope.deleteId = data;
    $scope.modalInstance = $uibModal.open({
      animation: $scope.animationsEnabled,
      templateUrl: 'views/modal/delete.html',
      backdrop: 'static',
      keyboard: false,
      size: 'sm',
      scope: $scope
    });
  };


  $scope.noDelete = function () {
    $scope.modalInstance.close();
  }

  $scope.delete = function (id) {
    // console.log(data);
    var url = "/sub_categories/delete";
    var obj = {};
    obj.id = id;
    console.log(obj);
    NavigationService.delete(url, obj, function (data) {
      if (data == '1' || data == "true") {
        toastr.success('Successfully Deleted', 'Sponsor Page');
        $scope.modalInstance.close();
        $scope.getAllSubCategories();
      } else {
        toastr.error('Something Went Wrong while Deleting', 'Sponsor Page');
      }
    });
  }
  // DELETE END
});

myApp.controller('DetailSubCategoryCtrl', function ($scope, TemplateService, NavigationService, $timeout, $stateParams, $state, toastr) {
  //Used to name the .html file
  $scope.template = TemplateService.changecontent("detailsubcategory");
  $scope.menutitle = NavigationService.makeactive("Special Award");
  TemplateService.title = $scope.menutitle;
  $scope.navigation = NavigationService.getnav();
  $scope.title = 'Create';
  $scope.formData = {};
  $scope.category = {
    'selectedCategory': {}
  };
  NavigationService.getAllCategories(function (data) {
    console.log(data);
    $scope.categories = data;
    // $scope.maxRow = data.data.options.count;
  });

  if ($stateParams.id) {
    $scope.title = "Edit";
    NavigationService.getOneSubCategory($stateParams.id, function (data) {
      console.log("data", data);
      $scope.formData = {
        "id": data[0]._id,
        "name": data[0].sub_cat_name,
        "category": data[0].cat_id
      };

      $scope.category = {
        "selectedCategory": {
          "id": data[0].cat_id,
          "name": data[0].cat_name
        }
      }
    });
  }


  $scope.saveData = function (data) {
    var url = "";
    data.category = $scope.category.selectedCategory.id;
    if ($stateParams.id) {
      //edit
      url = adminurl + "/sub_categories/update"
      var callback = function (data) {
        if (data == '1' || data == 'true') {
          toastr.success("Data updated successfully", 'Success');
        }
      }
    } else {
      //create
      url = adminurl + "/sub_categories/create"
      var callback = function (data) {
        if (data == '1' || data == 'true') {
          toastr.success("Data saved successfully", 'Success');
          $scope.formData._id = "";
          $scope.formData.name = "";
          $scope.formData.priority = "";
        }
      }
    }
    NavigationService.saveSubCategory(url, data, callback);
  }

  $scope.onCancel = function (sendTo) {

    $state.go(sendTo);
  };
});

myApp.controller('ProductsCtrl', function ($scope, TemplateService, NavigationService, $timeout, $stateParams, $state, toastr, $uibModal) {
  //Used to name the .html file
  $scope.template = TemplateService.changecontent("products");
  $scope.menutitle = NavigationService.makeactive("Products");
  TemplateService.title = $scope.menutitle;
  $scope.navigation = NavigationService.getnav();
  $scope.configData = {};

  $scope.domain = domain;

  $scope.formData = {};
  $scope.formData.page = 1;
  $scope.formData.type = '';
  $scope.formData.keyword = '';

  $scope.viewTable = function () {
    $scope.formData.page = $scope.formData.page++;
    NavigationService.getAll("/products/getAll", $scope.formData, function (data) {
      $scope.items = data.result;
      $scope.totalItems = data.totalCount;
    });
  }
  $scope.viewTable();

  // DELETE
  $scope.confDel = function (data) {
    $scope.deleteId = data;
    $scope.modalInstance = $uibModal.open({
      animation: $scope.animationsEnabled,
      templateUrl: 'views/modal/delete.html',
      backdrop: 'static',
      keyboard: false,
      size: 'sm',
      scope: $scope
    });
  };

  $scope.noDelete = function () {
    $scope.modalInstance.close();
  };

  $scope.delete = function (id) {
    // console.log(data);
    var url = "/products/delete";
    var obj = {};
    obj.id = id;
    console.log(obj);
    NavigationService.delete(url, obj, function (data) {
      if (data == '1' || data == "true") {
        toastr.success('Successfully Deleted', 'Sponsor Page');
        $scope.modalInstance.close();
        $scope.getAllProducts();
      } else {
        toastr.error('Something Went Wrong while Deleting', 'Sponsor Page');
      }
    });
  };

  $scope.changeStatus = function (item) {
    if (item.prod_status == 1) {
      item.prod_status = 0;
    } else {
      item.prod_status = 1;
    }
    // item.prod_status = ! item.prod_status;
    url = adminurl + "/products/update";
    var formData= {
      "id":item._id,
      "status":item.prod_status
    }
    var callback = function (data) {
      if (data == '1' || data == 'true') {
        toastr.success("Data updated successfully", 'Success');
      }
    }
    NavigationService.saveProducts(url, formData, callback);
  }

});

myApp.controller('DetailProductsCtrl', function ($scope, TemplateService, NavigationService, $timeout, $stateParams, $state, toastr, $uibModal) {
  //Used to name the .html file
  $scope.template = TemplateService.changecontent("detailproducts");
  $scope.menutitle = NavigationService.makeactive("Special Award");
  TemplateService.title = $scope.menutitle;
  $scope.navigation = NavigationService.getnav();
  $scope.formData = {};
  $scope.subCategory = {
    "selectedSubCategory": {}
  }
  $scope.domain = domain;

  NavigationService.getAllCategories(function (data) {
    console.log(data);
    $scope.categories = data;
    // $scope.maxRow = data.data.options.count;
  });

  $scope.searchSubCategories = function (_id) {
    $scope.subCategory={};
    var obj = {
      "category": _id
    }
    console.log(obj);
    NavigationService.getAllByCat(obj, function (data) {
      $scope.subCategories = data;
    })
  }

  if ($stateParams.id) {
    $scope.title = "Edit";
    NavigationService.getOneProduct($stateParams.id, function (data) {
      console.log("data", data);
      $scope.searchSubCategories(data[0].cat_id);
      $scope.formData = {
        "id": data[0]._id,
        "name": data[0].prod_name,
        "priority": data[0].prod_priority,
        "status": data[0].prod_status,
        "link": data[0].prod_link
      };

      $scope.category = {
        "selectedCategory": {
          "id": data[0].cat_id,
          "name": data[0].cat_name
        }
      }

      $scope.subCategory = {
        "selectedSubCategory": {
          "id": data[0].sub_cat_id,
          "name": data[0].sub_cat_name
        }
      }
    });
  }


  $scope.saveData = function (data) {
    var url = "";
    data.sub_categories = $scope.subCategory.selectedSubCategory.id;
    if ($stateParams.id) {
      //edit
      url = adminurl + "/products/update"
      var callback = function (data) {
        if (data == '1' || data == 'true') {
          toastr.success("Data updated successfully", 'Success');
        }
      }
    } else {
      //create
      url = adminurl + "/products/create"
      var callback = function (data) {
        if (data == '1' || data == 'true') {
          toastr.success("Data saved successfully", 'Success');
          $scope.formData._id = "";
          $scope.formData.name = "";
          $scope.formData.priority = "";
          $scope.forData.satus = "";
          $scope.subCategories.selectedSubCategory = {};
        }
      }
    }
    NavigationService.saveProducts(url, data, callback);
  }

  $scope.onCancel = function (sendTo) {

    $state.go(sendTo);
  };
});

myApp.controller('ContactsCtrl', function ($scope, TemplateService, NavigationService, $timeout, $stateParams, $state, toastr, $uibModal) {
  //Used to name the .html file
  $scope.template = TemplateService.changecontent("contacts");
  $scope.menutitle = NavigationService.makeactive("Contacts");
  TemplateService.title = $scope.menutitle;
  $scope.navigation = NavigationService.getnav();

  $scope.formData = {};
  $scope.formData.page = 1;
  $scope.formData.type = '';
  $scope.formData.keyword = '';

  $scope.searchInTable = function (data) {
    $scope.formData.page = 1;
    if (data.length >= 2) {
      $scope.formData.keyword = data;
      $scope.viewTable();
    } else if (data.length == '') {
      $scope.formData.keyword = data;
      $scope.viewTable();
    }
  }
  $scope.viewTable = function () {
    $scope.formData.page = $scope.formData.page++;
    NavigationService.getAll("/contacts/getAll", $scope.formData, function (data) {
      $scope.items = data.result;
      $scope.totalItems = data.totalCount;
    });
  }
  $scope.viewTable();

  // DELETE
  $scope.confDel = function (data) {
    $scope.deleteId = data;
    $scope.modalInstance = $uibModal.open({
      animation: $scope.animationsEnabled,
      templateUrl: 'views/modal/delete.html',
      backdrop: 'static',
      keyboard: false,
      size: 'sm',
      scope: $scope
    });
  };

  $scope.noDelete = function () {
    $scope.modalInstance.close();
  }

  $scope.delete = function (id) {
    // console.log(data);
    var obj = {};
    obj.id = id;
    NavigationService.delete("/contacts/delete", obj, function (data) {
      if (data == '1' || data == "true") {
        toastr.success('Successfully Deleted', 'Sponsor Page');
        $scope.modalInstance.close();
        $scope.getAll();
      } else {
        toastr.error('Something Went Wrong while Deleting', 'Sponsor Page');
      }
    });
  }
  // DELETE END
});

myApp.controller('CreateContactsCtrl', function ($scope, TemplateService, NavigationService, $timeout, $stateParams, $state, toastr, $uibModal) {
  //Used to name the .html file
  $scope.template = TemplateService.changecontent("createcontacts");
  $scope.menutitle = NavigationService.makeactive("Contacts");
  TemplateService.title = $scope.menutitle;
  $scope.navigation = NavigationService.getnav();
  $scope.formData = {};


  if ($stateParams.id) {
    $scope.title = "Edit";
    var data = {
      "_id": $stateParams.id
    }
    NavigationService.getOne('/contacts/getOne', data, function (data) {
      console.log("formData.number", data[0].number);
      $scope.formData = {
        "_id": data[0]._id,
        "name": data[0].name,
        "number": parseInt(data[0].number),
        "email": data[0].email,
        "address": data[0].address
      };
    });
  }


  $scope.saveData = function (data) {
    var url = "";
    if ($stateParams.id) {
      //edit
      url = adminurl + "/contacts/update"
      var callback = function (data) {
        if (data == '1' || data == 'true') {
          toastr.success("Data updated successfully", 'Success');
        } else {
          toastr.error("Number Already Exists", 'Error');
        }
      }
    } else {
      //create
      url = adminurl + "/contacts/create"
      var callback = function (data) {
        if (data == '1' || data == 'true') {
          toastr.success("Data saved successfully", 'Success');
          $scope.formData._id = "";
          $scope.formData.name = "";
          $scope.formData.number = "";
          $scope.formData.email = "";
          $scope.formData.address = "";
        } else {
          toastr.error("Number Already Exists", 'Error');
        }
      }
    }
    NavigationService.saveProducts(url, data, callback);
  }

  $scope.onCancel = function (sendTo) {

    $state.go(sendTo);
  };
});

myApp.controller('ContactsEntryUserCtrl', function ($scope, TemplateService, NavigationService, $timeout, $stateParams, $state, toastr, $uibModal) {
  //Used to name the .html file
  // $scope.template = TemplateService.changecontent("contactsEntryUser");
  $scope.menutitle = NavigationService.makeactive("Contacts Entry");
  TemplateService.title = $scope.menutitle;
  $scope.navigation = NavigationService.getnav();
  $scope.formData = {};


  if ($stateParams.id) {
    $scope.title = "Edit";
    var data = {
      "_id": $stateParams.id
    }
    NavigationService.getOne('/contacts/getOne', data, function (data) {
      console.log("formData.number", data[0].number);
      $scope.formData = {
        "_id": data[0]._id,
        "name": data[0].name,
        "number": parseInt(data[0].number),
        "email": data[0].email,
        "address": data[0].address
      };
    });
  }


  $scope.saveData = function (data) {
    var url = "";
    if ($stateParams.id) {
      //edit
      url = adminurl + "/contacts/update"
      var callback = function (data) {
        if (data == '1' || data == 'true') {
          toastr.success("Data updated successfully", 'Success');
        } else {
          toastr.error("Number Already Exists", 'Error');
        }
      }
    } else {
      //create
      url = adminurl + "/contacts/create"
      var callback = function (data) {
        if (data.status == '1' || data.status == 'true') {
          toastr.success("Data saved successfully", 'Success');
          $scope.formData._id = "";
          $scope.formData.name = "";
          $scope.formData.companyName = "";
          $scope.formData.number = "";
          $scope.formData.email = "";
          $scope.formData.address = "";
          $scope.formData.website = "";
          $scope.formData.note = "";
          toastr.success(data.id);
        } else {
          toastr.error("Number Already Exists", 'Error');
        }
      }
    }
    NavigationService.saveProducts(url, data, callback);
  }

  $scope.onCancel = function (sendTo) {

    $state.go(sendTo);
  };
});

myApp.controller('BannerCtrl', function ($scope, TemplateService, NavigationService, $timeout, $stateParams, $state, toastr, $uibModal) {
  //Used to name the .html file
  $scope.template = TemplateService.changecontent("banners");
  $scope.menutitle = NavigationService.makeactive("Banners");
  TemplateService.title = $scope.menutitle;
  $scope.navigation = NavigationService.getnav();
  $scope.formData = {};

  $scope.domain = domain;

  $scope.viewTable = function () {
    NavigationService.getAll("/banners/getAll", $scope.formData, function (data) {
      $scope.items = data;
    });
  }
  $scope.viewTable();

  // DELETE
  $scope.confDel = function (data) {
    $scope.deleteId = data;
    $scope.modalInstance = $uibModal.open({
      animation: $scope.animationsEnabled,
      templateUrl: 'views/modal/delete.html',
      backdrop: 'static',
      keyboard: false,
      size: 'sm',
      scope: $scope
    });
  };

  $scope.noDelete = function () {
    $scope.modalInstance.close();
  }

  $scope.delete = function (id) {
    // console.log(data);
    var url = "/banners/delete";
    var obj = {};
    obj.id = id;
    NavigationService.delete(url, obj, function (data) {
      if (data == '1' || data == "true") {
        toastr.success('Successfully Deleted', 'Sponsor Page');
        $scope.modalInstance.close();
        $scope.viewTable();
      } else {
        toastr.error('Something Went Wrong while Deleting', 'Sponsor Page');
      }
    });
  }
  // DELETE END

  $scope.testBanner = function () {
    if ($scope.formData.link) {
      NavigationService.create("/banners/create", $scope.formData.link, function (data) {
        console.log("data", data);
      });
    }
  }


});
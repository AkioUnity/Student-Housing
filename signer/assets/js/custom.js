$(document).ready(function () {

    jQuery.validator.addMethod("specialChars", function( value, element ) {
        var regex = /^[0-9a-zA-Z\_\-\.]+$/;
        regex = new RegExp(regex);
        var key = value;

        if (!regex.test(key)) {
            return false;
        }
        return true;
    }, "Please enter valid text");


    jQuery.validator.addMethod("searchInputRegex", function(value, element) {
//        return this.optional(element) || /^[a-z0-9A-Z\_\-\.\ \s]+$/i.test(value);
        return this.optional(element) || /^[a-z0-9A-Z\_\-\.\ \s\+\#\,\']+|[一-龠]+|[ぁ-ゔ]+|[ァ-ヴー]+|[^\x00-\x7F]+|[a-z\u0590-\u05fe]+|[àáãạảăắằẳẵặâấầẩẫậèéẹẻẽêềếểễệđìíĩỉịòóõọỏôốồổỗộơớờởỡợùúũụủưứừửữựỳỵỷỹýÀÁÃẠẢĂẮẰẲẴẶÂẤẦẨẪẬÈÉẸẺẼÊỀẾỂỄỆĐÌÍĨỈỊÒÓÕỌỎÔỐỒỔỖỘƠỚỜỞỠỢÙÚŨỤỦƯỨỪỬỮỰỲỴỶỸÝ]+$/i.test(value);
}, "Please enter valid text");


    jQuery.validator.addMethod("inputRegex", function(value, element) {
//        return this.optional(element) || /^[a-z0-9A-Z\_\-\.\ \sàáãạảăắằẳẵặâấầẩẫậèéẹẻẽêềếểễệđìíĩỉịòóõọỏôốồổỗôộơớờởỡợùúũụủủưứừửữựỳỵỷỹýÀÁÃẠẢĂẮẰẲẴẶÂẤẦẨẪẬÈÉẸẺẼÊỀẾỂỄỆĐÌÍĨỈỊÒÓÕỌỎÔỐỒỔỖỘƠỚỜỞỠỢÙÚŨỤỦƯỨỪỬỮỰỲỴỶỸÝ]+$/i.test(value);
        return this.optional(element) || /^[a-z0-9A-Z\_\-\.\ \s\+\#\,\']+|[一-龠]+|[ぁ-ゔ]+|[ァ-ヴー]+|[^\x00-\x7F]+|[a-z\u0590-\u05fe]+|[àáãạảăắằẳẵặâấầẩẫậèéẹẻẽêềếểễệđìíĩỉịòóõọỏôốồổỗộơớờởỡợùúũụủưứừửữựỳỵỷỹýÀÁÃẠẢĂẮẰẲẴẶÂẤẦẨẪẬÈÉẸẺẼÊỀẾỂỄỆĐÌÍĨỈỊÒÓÕỌỎÔỐỒỔỖỘƠỚỜỞỠỢÙÚŨỤỦƯỨỪỬỮỰỲỴỶỸÝ]+$/i.test(value);

    }, "Please enter valid text");

    //Begin:: Header search form
    $('.search-form').validate({
        rules: {
            search: {
                searchInputRegex: true,
            }
        },
        message: {
        }
    });
    //End:: Header search form

    //Begin:: Create folder form
    $('#create-folder-form').validate({
        rules: {
            name: {
                inputRegex: true,
            }
        },
        message: {
        }
    });
    //End:: Create folder form

    // Begin:: Upload folder form
    $('#upload-file-form').validate({
        rules: {
            name: {
                inputRegex: true,
            }
        },
        message: {
        }
    });
    //End:: Upload folder form

    // Begin:: Rename folder form
    $('#rename-folder-form').validate({
        rules: {
            foldername: {
                inputRegex: true,
            }
        },
        message: {
        }
    });
    //End:: Rename folder form

    // Begin:: Rename file form
    $('#rename-file-form').validate({
        rules: {
            filename: {
                inputRegex: true,
            }
        },
        message: {
        }
    });
    //End:: Rename file form

    // Begin:: Template upload file form
    $('#template-upload-file-form').validate({
        rules: {
            name: {
                inputRegex: true,
            }
        },
        message: {
        }
    });
    //End:: Template upload file form

    // Begin:: Template upload file form
    $('#template-rename-file-form').validate({
        rules: {
            filename: {
                inputRegex: true,
            }
        },
        message: {
        }
    });
    //End:: Template upload file form

    // Begin:: Create customer form
    $('#create-customer-form').validate({
        rules: {
            fname: {
                inputRegex: true,
            },
            lname: {
                inputRegex: true,
            },
            phone: {
                inputRegex: true,
            },
            address: {
                inputRegex: true,
            },
        },
        message: {
        }
    });
    //End:: Create customer form

    // Begin:: Update customer form
    $('#update-customer-form').validate({
        rules: {
            fname: {
                inputRegex: true,
            },
            lname: {
                inputRegex: true,
            },
            phone: {
                inputRegex: true,
            },
            address: {
                inputRegex: true,
            },
        },
        message: {
        }
    });
    //End:: Update customer form

    // Begin:: Create department form
    $('#create-department-form').validate({
        rules: {
            name: {
                inputRegex: true,
            }
        },
        message: {
        }
    });
    //End:: Create department form

    // Begin:: Update department form
    $('#update-department-form').validate({
        rules: {
            name: {
                inputRegex: true,
            }
        },
        message: {
        }
    });
    //End:: Update department form

    // Begin:: Create team form
    $('#create-team-form').validate({
        rules: {
            fname: {
                inputRegex: true,
            },
            lname: {
                inputRegex: true,
            },
            phone: {
                inputRegex: true,
            }
        },
        message: {
        }
    });
    //End:: Create team form

    // Begin:: Update team form
    $('#update-team-form').validate({
        rules: {
            fname: {
                inputRegex: true,
            },
            lname: {
                inputRegex: true,
            },
            phone: {
                inputRegex: true,
            }
        },
        message: {
        }
    });
    //End:: Update team form

    // Begin:: Update company form
    $('#update-company-form').validate({
        rules: {
            name: {
                inputRegex: true,
            },
            phone: {
                inputRegex: true,
            }
        },
        message: {
        }
    });
    //End:: Update company form

    // Begin:: Create user form
    $('#create-user-form').validate({
        rules: {
            fname: {
                inputRegex: true,
            },
            lname: {
                inputRegex: true,
            },
            address: {
                inputRegex: true,
            },
            phone: {
                inputRegex: true,
            }
        },
        message: {
        }
    });
    //End:: Create user form

    // Begin:: Update user form
    $('#update-user-form').validate({
        rules: {
            fname: {
                inputRegex: true,
            },
            lname: {
                inputRegex: true,
            },
            address: {
                inputRegex: true,
            },
            phone: {
                inputRegex: true,
            }
        },
        message: {
        }
    });
    //End:: Update user form

    // Begin:: Register form
    $('#register-form').validate({
        rules: {
            fname: {
                inputRegex: true,
            },
            lname: {
                inputRegex: true,
            },
            address: {
                inputRegex: true,
            },
            phone: {
                inputRegex: true,
            },
            company: {
                inputRegex: true,
            },

        },
        message: {
        }
    });
    //End:: Register form

    // Begin:: Setting profile form
    $('#setting-profile-form').validate({
        rules: {
            fname: {
                inputRegex: true,
            },
            lname: {
                inputRegex: true,
            },
            address: {
                inputRegex: true,
            },
            phone: {
                inputRegex: true,
            }
        },
        message: {
        }
    });
    //End:: Setting profile form

    // Begin:: Setting company form
    $('#setting-company-form').validate({
        rules: {
            name: {
                inputRegex: true,
            },
            phone: {
                inputRegex: true,
            }
        },
        message: {
        }
    });
    //End:: Setting company form

    //Begin:: Setting reminder form
    $('#setting-reminder-form').validate({
        rules: {
            'subject[]': {
                inputRegex: true,
            }
        },
        message: {
        }
    });
    //End:: Setting reminder form

});

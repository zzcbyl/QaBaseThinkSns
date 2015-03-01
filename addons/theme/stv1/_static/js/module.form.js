/**
 * 异步提交表单
 * @param object form 表单DOM对象
 * @return void
 */

var myReg = /^[-._A-Za-z0-9]+@([_A-Za-z0-9]+\.)+[A-Za-z0-9]{2,3}$/; 
var mobileReg = /^0*(13|15|18)\d{9}$/;
var ajaxSubmit = function (form) {
    var args = M.getModelArgs(form);
    M.getJS(THEME_URL + '/js/jquery.form.js', function () {
        var options = {
            dataType: "json",
            success: function (txt) {
                if (1 == txt.status) {
                    if ("function" === typeof form.callback) {
                        form.callback(txt);
                    } else {
                        if ("string" == typeof (args.callback)) {
                            eval(args.callback + '()');
                        } else {
                            ui.success(txt.info);
                            //修改密码
                            if ($("#UpdUserPwd").html().indexOf('修改密码') > 0) {
                                hideupdpwd();
                            }
                        }
                    }
                } else {
                    ui.error(txt.info);
                }
            }
        };
        $(form).ajaxSubmit(options);
    });
};

(function(){
// 是否点击了发送按钮
var isSubmit = 0;
// 块状模型监听
M.addModelFns({
	account_save:{
		callback:function(){
			ui.success(L('PUBLIC_ADMIN_OPRETING_SUCCESS'));
			setTimeout(function() {
				location.href = location.href;
			}, 1500);
		}
	},
	verify_apply:{
		callback:function(){
			ui.success('申请成功，请等待审核');
			setTimeout(function() {
				location.href = U('public/Account/authenticate');
			}, 1500);
		}
	},
	// 普通表单发送验证
	normal_form: {
		submit: function() {
			isSubmit = 1;
			var oCollection = this.elements;
			var nL = oCollection.length;
			var bValid = true;
			var dFirstError;
            
			for(var i = 0; i < nL; i++) {
				var dInput = oCollection[i];
				var sName = dInput.name;
				// 如果没有事件节点，则直接略过检查
				if(!sName || !dInput.getAttribute("event-node")) {
					continue;
				}

				("function" === typeof(dInput.onblur)) && dInput.onblur();

				if(!dInput.bIsValid) {
                    //alert($(dInput).attr('name'));
					bValid = false;
					if(dInput.type != 'hidden') {
						dFirstError = dFirstError || dInput;
					}
				}
			}

			dFirstError && dFirstError.focus();
			setTimeout(function() {
				isSubmit = 0;
			}, 1500);

			return bValid;
		}
	}
});
// 事件模型监听
M.addEventFns({
	// 文本框输入文本验证
	input_text: {
		focus: function() {
			this.className='s-txt-focus';
            this.focus();
			return false;
		},
		blur: function() {
			this.className = 's-txt';
			// 设置文本框的最大与最小输入限制
			var oArgs = M.getEventArgs( this );
			var	min = oArgs.min ? parseInt( oArgs.min ) : 0;
			var	max = oArgs.max ? parseInt( oArgs.max ) : 0;
			// 最大和最小长度均小于或等于0，则不进行长度验证
			if(min <= 0 && max <= 0) {
				return false;
			}

			var dTips = (this.parentModel.childEvents[this.getAttribute( "name" ) + "_tips"] || [])[0];
			var sValue = this.value;
			sValue = sValue.replace(/(^\s*)|(\s*$)/g, "");	
			var nL = sValue.replace(/[^\x00-\xff]/ig,'xx').length / 2;

			if(nL <= min-1 || ( max && nL > max)) {
				dTips && (dTips.style.display = "none");
				tips.error(this, oArgs.error);
				this.bIsValid = false;
			} else {
                if(sValue != '')
                {
				    tips.success(this);
				    dTips && (dTips.style.display = "");
				    
                }
                this.bIsValid = true;
			}
			return false;
		},
		load: function() {
			this.className='s-txt';
		}
	},
	// 文本框输入纯数字文本验证
	input_nums: {
		focus: function() {
			this.className = 's-txt-focus';
            this.focus();
			return false;
		},
		blur: function() {
			this.className = 's-txt';
			// 设置文本框的最大与最小输入限制
			var oArgs = M.getEventArgs(this);
			var min = oArgs.min ? parseInt( oArgs.min ) : 0;
			var max = oArgs.max ? parseInt( oArgs.max ) : 0;
			// 最大和最小长度均小于或等于0，则不进行长度验证
			if(min <= 0 && max <= 0) {
				return false;
			}

			var dTips = (this.parentModel.childEvents[this.getAttribute( "name" ) + "_tips"] || [])[0];
			var sValue = this.value;
            
			// 纯数字验证
			var re = /^[0-9]*$/;
			if(!re.test(sValue)) {
				dTips && (dTips.style.display = "none");
				tips.error(this, L('PUBLIC_TYPE_ISNOT'));		// 格式不正确
				this.bIsValid = false;
				return false;
			}

			sValue = sValue.replace(/(^\s*)|(\s*$)/g, "");	
			var nL = sValue.replace(/[^\x00-\xff]/ig, 'xx').length / 2;

			if(nL <= min-1 || (max && nL > max)) {
				dTips && (dTips.style.display = "none");
				tips.error(this, oArgs.error);
				this.bIsValid = false;
			} else {
                if(sValue != '')
                {
				    tips.success(this);
				    dTips && (dTips.style.display = "");
                }
                else
                {
                    tips.clear(this);
                }
                this.bIsValid = true;
			}

			return false;
		},
		load: function() {
			this.className = 's-txt';
		}
	},
	// 文本域验证
	textarea: {
		focus: function() {
			this.className = 's-textarea-focus';
		},
		blur: function() {
			this.className = 's-textarea';
			// 设置文本框的最大与最小输入限制
			var oArgs = M.getEventArgs(this);
			var min = oArgs.min ? parseInt( oArgs.min ) : 0;
			var max = oArgs.max ? parseInt( oArgs.max ) : 0;
			// 最大和最小长度均小于或等于0，则不进行长度验证
			if(min <= 0 && max <= 0) {
				return false;
			}

			if($.trim(this.value)) {
				tips.success(this);
				this.bIsValid = true;
			} else {
				if("undefined" != typeof(oArgs.error )) {
					tips.error(this, oArgs.error);
					this.bIsValid = false;
				}
			}
		},
		load: function() {
			this.className = 's-textarea';
		}
	},
	// 部门信息验证
	input_department: {
		blur: function() {
			var sValue = this.value;
			sValue && (sValue = parseInt(sValue));

			var dLastEmlement = this.nextSibling;
			(1 !== dLastEmlement.nodeType) && (dLastEmlement = dLastEmlement.nextSibling);
			if(sValue) {
				tips.success(dLastEmlement);
				this.bIsValid = true;
			} else {
				tips.error(dLastEmlement, L('PUBLIC_SELECT_DEPARTMENT'));
				this.bIsValid = false;
			}
		},
		load:function(){
			if("undefined" != typeof(core.department)) {
				delete core.department;
			}
			core.plugInit('department', $(this), $(this));
		}
	},
//	// 地区信息验证
//	input_area: {
//		blur: function() {
//			// 获取数据
//			var sValue = $.trim(this.value);
//			var sValueArr = sValue.split(",");
//			// 验证数据正确性
//			if(sValue == "" || sValueArr[0] == 0) {
//				tips.error(this, "请选择地区");
//				this.bIsValid = false;
//				this.value = '0,0,0';
//			} else if(sValueArr[1] == 0 || sValueArr[2] == 0) {
//				tips.error(this, "请选择完整地区信息");
//				this.bIsValid = false;
//			} else {
//				tips.success(this);
//				this.bIsValid = true;
//			}
//		},
//		load: function() {
//			// 获取参数信息
//			var _this = this;
//			// 验证数据正确性
//			setInterval(function() {
//				// 获取数据
//				var sValue = $.trim(_this.value);
//				var sValueArr = sValue.split(",");
//				// 验证数据正确性
//				if(sValue == "" || sValueArr[0] == 0) {
//					tips.error(_this, "请选择地区");
//					_this.bIsValid = false;
//				} else if(sValueArr[1] == 0 || sValueArr[2] == 0) {
//					tips.error(_this, "请选择完整地区信息");
//					_this.bIsValid = false;
//				} else {
//					tips.success(_this);
//					_this.bIsValid = true;
//				}
//			}, 200);
//		}
//	},
	// 时间格式验证
	input_date: {
		focus: function() {
			this.className = 's-txt-focus';

			var dDate = this;
			var oArgs = M.getEventArgs(this);

			M.getJS(THEME_URL + '/js/rcalendar.js', function() {
				rcalendar(dDate, oArgs.mode);
			});
		},
		blur: function() {
			this.className = 's-txt';

			var dTips = (this.parentModel.childEvents[this.getAttribute( "name" ) + "_tips"] || [])[0];
			var oArgs = M.getEventArgs(this);
			if(oArgs.min == 0) {
				return true;
			}		
			var _this = this;	
			setTimeout(function() {
				sValue = _this.value;
				if(!sValue) {
					dTips && (dTips.style.display = "none");
					tips.error(_this, oArgs.error);
					this.bIsValid = false;
				} else {
					tips.success(_this);
					dTips && (dTips.style.display = "");
					_this.bIsValid = true;
				}
			}, 250);
		},
		load: function() {
			this.className = 's-txt';
		}
	},
    account:    {
        focus: function() {
			this.className = 's-txt-focus';
			//var x = $(this).offset();
			//$(this.dTips).css({'position':'absolute','left':x.left+'px','top':x.top+$(this).height()+12+'px','width':$(this).width()+10+'px'});
		},
		blur: function() {
            var dAccount = this;
			var sUrl = dAccount.getAttribute("checkurl");
			var sValue = dAccount.value;
            //alert(sValue);
			if(!sUrl || (this.dSuggest && this.dSuggest.isEnter)) {
				return false;
			}
            if(sValue.length==0)
            {
                tips.error( dAccount, '帐号不能为空' );
                $("#emailDiv").hide();
                $("#mobileDiv").hide(); 
                //$("#yzmDiv").hide(); 
//                if (from == "sina" || from == "qzone") {
//                    $("#yqmCode").hide();
//                    $("#yqmCodeText").hide();
//                }
//                else {
//                    $("#yqmCode").show();
//                    $("#yqmCodeText").show();
//                }
                return false;
            }

            if(!myReg.test(sValue) && !mobileReg.test(sValue))
            {
                tips.error( dAccount, '帐号只能是手机号或者邮箱' );
                $("#emailDiv").hide();
                $("#mobileDiv").hide(); 
                //$("#yzmDiv").hide(); 
//                if (from == "sina" || from == "qzone") {
//                    $("#yqmCode").hide();
//                    $("#yqmCodeText").hide();
//                }
//                else {
//                    $("#yqmCode").show();
//                    $("#yqmCodeText").show();
//                }
                return false;
            }
            else
            {
                tips.clear( dAccount )
            }

            $.post(sUrl, {account:sValue}, function(oTxt) {
				var oArgs = M.getEventArgs(dAccount);
				if(oTxt.status) {
					"false" == oArgs.success ? tips.clear( dAccount ) : tips.success( dAccount );
					dAccount.bIsValid = true;
				} else {
					"false" == oArgs.error ? tips.clear( dAccount ) : tips.error( dAccount, oTxt.info );
					dAccount.bIsValid = false;
				}
				return true;
			}, 'json');
			$(this.dTips).hide();

            //邮箱
            if(myReg.test(sValue)) {
                $("#emailDiv").hide();
                $("#mobileDiv").show();
                //$("#yzmDiv").hide();
//                if (from == "sina" || from == "qzone") {
//                    $("#yqmCode").hide();
//                    $("#yqmCodeText").hide();
//                }
//                else {
//                    $("#yqmCode").show();
//                    $("#yqmCodeText").show();
//                }
            }
            //手机号
            else if(ismobile(sValue)) {
                $("#emailDiv").show();
                $("#mobileDiv").hide(); 
                //$("#yzmDiv").show(); 
                $("#yqmCode").hide();
                $("#yqmCodeText").hide();
            }
            else {
                $("#emailDiv").hide();
                $("#mobileDiv").hide(); 
                //$("#yzmDiv").hide(); 
//                if (from == "sina" || from == "qzone") {
//                    $("#yqmCode").hide();
//                    $("#yqmCodeText").hide();
//                }
//                else {
//                    $("#yqmCode").show();
//                    $("#yqmCodeText").show();
//                }
            }

        },
		load: function() {
            sValue = this.value;

            //邮箱
            if(myReg.test(sValue)) {
                $("#emailDiv").hide();
                $("#mobileDiv").show();
                //$("#yzmDiv").hide();
            }
            //手机号
            else if(ismobile(sValue)) {
                $("#emailDiv").show();
                $("#mobileDiv").hide(); 
                //$("#yzmDiv").show(); 
            }
            else {
                $("#emailDiv").hide();
                $("#mobileDiv").hide(); 
                //$("#yzmDiv").hide(); 
            }
			this.className = 's-txt';
        }
    },
	// 邮箱验证
	email: {
		focus: function() {
			this.className = 's-txt-focus';
			var x = $(this).offset();
			$(this.dTips).css({'position':'absolute','left':x.left+'px','top':x.top+$(this).height()+12+'px','width':$(this).width()+10+'px'});
		},
		blur: function() {
			this.className = 's-txt';

			var dEmail = this;
			var sUrl = dEmail.getAttribute("checkurl");
            var sMust = dEmail.getAttribute("ismust");
			var sValue = dEmail.value;
            //alert(sValue);
			if(!sUrl || (this.dSuggest && this.dSuggest.isEnter)) {
				return false;
			}
            if(sMust=='true')
            {
                if(sValue.length==0)
                {
                    tips.error( dEmail, '邮箱地址不能为空' );
                    return false;
                }
            }
            else
            {
                tips.clear( dEmail )
                if(sValue.length==0)
                    dEmail.bIsValid = true;
            }

            if(sValue.length!=0)
            {
                var myReg = /^[-._A-Za-z0-9]+@([_A-Za-z0-9]+\.)+[A-Za-z0-9]{2,3}$/; 
                if(!myReg.test(sValue))
                {
                    tips.error( dEmail, '无效的邮箱地址' );
                    return false;
                }
                else
                    tips.clear( dEmail )

			    $.post(sUrl, {email:sValue}, function(oTxt) {
				    var oArgs = M.getEventArgs(dEmail);
				    if(oTxt.status) {
					    "false" == oArgs.success ? tips.clear( dEmail ) : tips.success( dEmail );
					    dEmail.bIsValid = true;
				    } else {
					    "false" == oArgs.error ? tips.clear( dEmail ) : tips.error( dEmail, oTxt.info );
					    dEmail.bIsValid = false;
				    }
				    return true;
			    }, 'json');
			    $(this.dTips).hide();
            }
		},
		load: function() {
			this.className = 's-txt';

			var dEmail = this;
			var oArgs = M.getEventArgs(this);

			if(!oArgs.suffix) {
				return false;
			}

			var aSuffix = oArgs.suffix.split( "," );
			var dFrag = document.createDocumentFragment();
			var dTips = document.createElement( "div" );
			var dUl = document.createElement( "ul" );
			
			this.dTips = $(dTips);
		    $('body').append(this.dTips);

		    dTips.className = "mod-at-wrap";
			dDiv = dTips.appendChild(dTips.cloneNode(false));
			dDiv.className = "mod-at";
			dDiv = dDiv.appendChild(dTips.cloneNode(false));
			dDiv.className = "mod-at-list";
			dUl = dDiv.appendChild(dUl);
			dUl.className = "at-user-list";
			dTips.style.display = "none";
			dEmail.parentNode.appendChild(dFrag);

			M.addListener(dTips, {
				mouseenter: function() {
					this.isEnter = 1;
				},
				mouseleave: function() {
					this.isEnter = 0;
				}
			});

			// 附加到Input DOM 上
			dEmail.dSuggest = dTips;

			setInterval(function() {
				var sValue = dEmail.value;
				var sTips = dEmail.dSuggest;
				if(dEmail.sCacheValue === sValue) {
					return false;
				} else {
					// 缓存值
					dEmail.sCacheValue = sValue;
				}
				// 空值判断
				if(!sValue) {
					dTips.style.display = "none";
					return ;
				}
				var aValue = sValue.split("@");
				var dFrag = document.createDocumentFragment();
				var l = aSuffix.length;
				var sSuffix;

				sInputSuffix = ["@",aValue[1]].join(""); // 用户输入的邮箱的后缀

				for(var i = 0; i < l; i ++) {
					sSuffix = aSuffix[i];
					if(aValue[1] && ( "" != aValue[1] ) && (sSuffix.indexOf(aValue[1]) !== 1 ) || (sSuffix === sInputSuffix)) {
						continue;
					}
					var dLi = dLi ? dLi.cloneNode(false) : document.createElement("li");
					var dA = dA ? dA.cloneNode(false) : document.createElement("a");
					var dSpan = dSpan ? dSpan.cloneNode(false) : document.createElement("span");
					var dText = dText ? dText.cloneNode(false) : document.createTextNode("");

					dText.nodeValue = [aValue[0], sSuffix].join("");

					dSpan.appendChild(dText);

					dA.appendChild(dSpan);

					dLi.appendChild(dA);

					dLi.onclick = (function(dInput, sValue, sSuffix) {
						return function(e) {
							dInput.value = [ sValue, sSuffix ].join( "" );
							// 选择完毕，状态为离开选择下拉条
							dTips.isEnter = 0;
							// 自动验证
							dInput.onblur();
							return false;
						};
					})(dEmail, aValue[0], sSuffix);

					dFrag.appendChild(dLi);
				}
				if(dLi) {
					dUl.innerHTML = "";
					dUl.appendChild( dFrag );
					dTips.style.display = "";
					$(dUl).find('li').hover(function() {
						$(this).addClass('hover');
					},function() {
						$(this).removeClass('hover');
					});

				} else {
					dTips.style.display = "none";
				}
			}, 200);
		}
	},
	// 密码验证
	password: {
		focus: function() {
			this.className = 's-txt-focus';
		},
		blur: function() {
			var dWeight = this.parentModel.childModels["password_weight"][0];
			var sValue = this.value + "";
			var nL = sValue.length;
			var min = 6
			var max = 15;
			if ( nL < min ) {
				dWeight.style.display = "none";
				tips.error( this, L('PUBLIC_PASSWORD_TIPES_MIN',{'sum':min}));
				this.bIsValid = false;
			} else if ( nL > max ) {
				dWeight.style.display = "none";
				tips.error( this, L('PUBLIC_PASSWORD_TIPES_MAX',{'sum':max}) );
				this.bIsValid = false;
			} else {
				tips.clear( this );
				dWeight.style.display = "";
				this.bIsValid = true;
				this.parentModel.childEvents["repassword"][0].onblur();
			}
		},
		keyup:function(){
			this.value = this.value.replace(/^\s+|\s+$/g,""); 
		},
		load: function() {
			this.value = '';
			this.className='s-txt';

			var dPwd = this,
				dWeight = this.parentModel.childModels["password_weight"][0],
				aLevel = [ "psw-state-empty", "psw-state-poor", "psw-state-normal", "psw-state-strong" ];

			setInterval( function() {
				var sValue = dPwd.value;
				// 缓存值
				if ( dPwd.sCacheValue === sValue ) {
					return ;
				} else {
					dPwd.sCacheValue = sValue;
				}
				// 空值判断
				if ( ! sValue ) {
					dWeight.className = aLevel[0];
					dWeight.setAttribute('className',aLevel[0]);
					return ;
				}
				var nL = sValue.length;

				if ( nL < 6 ) {
					dWeight.className = aLevel[0];
					dWeight.setAttribute('className',aLevel[0]);
					return ;
				}

				var nLFactor = Math.floor( nL / 10 ) ? 1 : 0;
				var nMixFactor = 0;

				sValue.match( /[a-zA-Z]+/ ) && nMixFactor ++;
				sValue.match( /[0-9]+/ ) && nMixFactor ++;
				sValue.match( /[^a-zA-Z0-9]+/ ) && nMixFactor ++;
				nMixFactor > 1 && nMixFactor --;

				dWeight.className = aLevel[nLFactor + nMixFactor];
				dWeight.setAttribute('className',aLevel[nLFactor + nMixFactor]);

			}, 200 );
		}
	},
	repassword: {
		focus: function() {
			this.className='s-txt-focus';
		},
		keyup:function(){
			this.value = this.value.replace(/^\s+|\s+$/g,""); 
		},
		blur: function() {
			this.className='s-txt';

			var sPwd = this.parentModel.childEvents["password"][0].value,
				sRePwd = this.value;

			if ( ! sRePwd ) {
				tips.error( this, L('PUBLIC_PLEASE_PASSWORD_ON') );
				this.bIsValid = false;
			} else if ( sPwd !== sRePwd ) {
				tips.error( this, L('PUBLIC_PASSWORD_ISDUBLE_NOT') );
				this.bIsValid = false;
			} else {
				tips.success( this );
				this.bIsValid = true;
			}
		},
		load: function() {
			this.className='s-txt';
		}
	},
    // 密码验证
	password_new: {
		focus: function() {
			this.className = 's-txt-focus';
		},
		blur: function() {
            this.className='s-txt';
			var sValue = this.value + "";
			var nL = sValue.length;
			var min = 6
			var max = 16;
			if ( nL < min || nL > max ) {
				tips.error( this, L('PUBLIC_PASSWORD_TIPES'));
				this.bIsValid = false;
            } else {
                tips.success( this );
				this.bIsValid = true;
			}
		},
		keyup:function(){
			this.value = this.value.replace(/^\s+|\s+$/g,""); 
		},
		load: function() {
			this.value = '';
			this.className='s-txt';
		}
	},
	repassword_new: {
		focus: function() {
			this.className='s-txt-focus';
		},
		keyup:function(){
			this.value = this.value.replace(/^\s+|\s+$/g,""); 
		},
		blur: function() {
			this.className='s-txt';
            //alert('123123');
			var sPwd = this.parentModel.childEvents["password_new"][0].value;
	        var sRePwd = this.value;
            //alert(sPwd);
            //alert(sRePwd);
			if ( ! sRePwd ) {
				tips.error( this, L('PUBLIC_PLEASE_PASSWORD_ON') );
				this.bIsValid = false;
			} else if ( sPwd !== sRePwd ) {
				tips.error( this, L('PUBLIC_PASSWORD_ISDUBLE_NOT') );
				this.bIsValid = false;
			} else {
				tips.clear( this );
                tips.success( this );
				this.bIsValid = true;
			}
		},
		load: function() {
			this.value = '';
			this.className='s-txt';
		}
	},
    //阅读服务条款
    readRules: {
		blur: function() {
			if ( !this.checked ) {
                tips.error( this, '请阅读并同意相关服务条款' );
				this.bIsValid = false;
            } else {
                tips.clear( this );
				this.bIsValid = true;
			}
		}
	},
    // 真实姓名验证
	realname: {
		focus: function() {
			this.className='s-txt-focus';
            this.focus();
			return false;
		},
		blur: function() {
			this.className='s-txt';
            this.bIsValid = true;
        },
        load: function() {
			this.className='s-txt';
		}
    },
    // 身份证
//	idcard: {
//		focus: function() {
//			this.className='s-txt-focus';
//			return false;
//		},
//		blur: function() {
//			this.className='s-txt';
//            
//			var idCardVal = this.value;
//            if(idCardVal != '')
//            {
//                var result = isIdCardNo(idCardVal);
//                if(result != '')
//                {
//                    tips.error( this, L(result) );
//				    this.bIsValid = false;
//                }
//                else
//                {
//                    tips.success( this );
//				    this.bIsValid = true;
//                }
//            }
//            else
//            {
//                tips.error( this, '请输入您的身份证号');
//				    this.bIsValid = false;
//            }
//        },
//        load: function() {
//			this.className='s-txt';
//		}
//    },
    // 手机号
    mobile: {
        focus: function() {
            this.className = 's-txt-focus';
            return false;
        },
        blur: function() {
            this.className = 's-txt';

            var mobile = this.value;
            var sMust = this.getAttribute("ismust");

            if (mobile != '') {
                var result = ismobile(mobile);
                if (!result) {
                    tips.error(this, '手机号格式有误');
                    this.bIsValid = false;
                }
                else {
                    tips.success(this);
                    this.bIsValid = true;
                }
            }
            else if(sMust == 'true') {
                tips.error(this, '手机号不能为空');
                this.bIsValid = false;
            }
            else
            {
                tips.clear(this);
                this.bIsValid = true;
             }
        },
        load: function() {
            this.className = 's-txt';
        }
    },
	// 昵称验证
	uname: {
		focus: function() {
			this.className='s-txt-focus';
			return false;
		},
		blur: function() {
            
			this.className='s-txt';

			var dUname = this;
			var sUrl = dUname.getAttribute('checkurl');
			var sValue = dUname.value;
			var oArgs = M.getEventArgs(dUname);
			var oValue = oArgs.old_name;

			if(!sUrl || (this.dSuggest && this.dSuggest.isEnter)) return;

            if(sValue.length==0)
                tips.error( dUname, '昵称不能为空' );
            else
                tips.clear(dUname)
            

			$.post(sUrl, {uname:sValue, old_name:oValue}, function(oTxt) {
				if(oTxt.status) {
					'false' == oArgs.success ? tips.clear(dUname) : tips.success(dUname);
					dUname.bIsValid = true;
				} else {
					'false' == oArgs.error ? tips.clear(dUname) : tips.error(dUname, oTxt.info);
					dUname.bIsValid = false;
				}
				return true;
			}, 'json');
			$(this.dTips).hide();
		},
		load: function() {
			this.className='s-txt';
		}
	},
//    yzmCode: {
//        focus: function() {
//			this.className='s-txt-focus';
//			return false;
//		},
//		blur: function() {
//            if($("#yzmDiv").css('display')!='none') {
//                this.className='s-txt';
//                var Code = this;
//                var BtnCode = document.getElementById("btnGetCode");
//                var sValue = Code.value;
//                var sUrl = Code.getAttribute('checkurl');
//                if(sValue.length==0) {
//                    tips.error( BtnCode, '验证码不能为空' );
//                    this.bIsValid = false;
//                }
//                else
//                {
//                    tips.clear(BtnCode)
//                    var smsCode = $("#SmsCode").val();
//                    if(smsCode != "")
//                    {
//                        if(sValue != smsCode)
//                        {
//                            tips.error( BtnCode, '验证码错误' );
//                            this.bIsValid = false;
//                        }
//                        else
//                        {
//                            tips.success(BtnCode);
//                            this.bIsValid = true;
//                        }
//                    }
//                    else
//                        this.bIsValid = true;
//                }
//            }
//            else
//               this.bIsValid = true;
//        },
//		load: function() {
//			this.className='s-txt';
//		}
//    },
    yqCode: {
        focus: function() {
			this.className='s-txt-focus';
			return false;
		},
		blur: function() {
            if($("#yqmCode").css('display')!='none') {
                this.className='s-txt';
                var Code = this;
                var sValue = Code.value;
                var sUrl = Code.getAttribute('checkurl');
                var oArgs = M.getEventArgs(Code);
                if(sValue.length==0) {
                    tips.error( Code, '邀请码不能为空' );
                    this.bIsValid = false;
                    return false;
                }
                else {
                    tips.clear(Code)
                }

                $.post(sUrl, { Code: sValue }, function(oTxt) {
				    if(oTxt.status == '1') {
					    'false' == oArgs.success ? tips.clear(Code) : tips.success(Code);
					    Code.bIsValid = true;
				    } else {
					    'false' == oArgs.error ? tips.clear(Code) : tips.error(Code, oTxt.data);
					    Code.bIsValid = false;
				    }
				    return true;
			    }, 'json');
			    $(this.dTips).hide();

           }
           else
               this.bIsValid = true;
        },
		load: function() {
			this.className='s-txt';
		}
    },
	radio: {
		click: function() {
			this.onblur();
		},
		blur: function() {
			var sName  = this.name,
				oRadio = this.parentModel.elements["sex"],
				oArgs  = M.getEventArgs( oRadio[0] ),
				dRadio, nL = oRadio.length, bIsValid = false,
				dLastRadio = oRadio[nL - 1];

			for ( var i = 0; i < nL; i ++ ) {
				dRadio = oRadio[i];
				if ( dRadio.checked ) {
					bIsValid = true;
					break;
				}
			}

			if ( bIsValid ) {
				tips.clear( dLastRadio.parentNode );
			} else {
				tips.error( dLastRadio.parentNode, oArgs.error );
			}

			for ( var i = 0; i < nL; i ++ ) {
				oRadio[i].bIsValid = bIsValid;
			}
		}
	},
	checkbox: {
		click: function() {
			this.onblur();
		},
		blur: function() {
			var oArgs = M.getEventArgs( this );
			if ( this.checked ) {
				tips.clear( this.parentNode );
				this.bIsValid = true;
			} else {
				tips.error( this.parentNode, oArgs.error );
				this.bIsValid = false;
			}
		}
	},
	submit_btn: {
		click: function(){
			var args  = M.getEventArgs(this);
			if ( args.info && ! confirm( args.info )) {
				return false;
			}
            
			try{
				(function( node ) {
					var parent = node.parentNode;
					// 判断node 类型，防止意外循环
					if ( "FORM" === parent.nodeName ) {
						if ( "false" === args.ajax ) {
							( ( "function" !== typeof parent.onsubmit ) || ( false !== parent.onsubmit() ) ) && parent.submit();
						} else {
							ajaxSubmit( parent );
						}
					} else if ( 1 === parent.nodeType ) {
						arguments.callee( parent );
					}
				})(this);
			}catch(e){
				return true;
			}
			return false;
		}
	},
	sendBtn: {
		click: function() {
			var parent = this.parentModel;
			return false;
		}
	},
    // 单位名称
	companyname: {
		focus: function() {
			this.className='s-txt-focus';
			return false;
		},
		blur: function() {
			this.className='s-txt';
        },
        load: function() {
			this.className='s-txt';
		}
    },
    // 部门/职位
	position: {
		focus: function() {
			this.className='s-txt-focus';
			return false;
		},
		blur: function() {
			this.className='s-txt';
        },
        load: function() {
			this.className='s-txt';
		}
    },
    // 学校名称
	schoolname: {
		focus: function() {
			this.className='s-txt-focus';
			return false;
		},
		blur: function() {
			this.className='s-txt';
        },
        load: function() {
			this.className='s-txt';
		}
    },
    // 院系
	department: {
		focus: function() {
			this.className='s-txt-focus';
			return false;
		},
		blur: function() {
			this.className='s-txt';
        },
        load: function() {
			this.className='s-txt';
		}
    }
});
/**
 * 提示语Js对象
 */
var tips = {
	/**
	 * 初始化，正确与错误提示
	 * @param object D DOM对象
	 * @return void
	 */
	init: function(D) {
		this._initError(D);
		this._initSuccess(D);
	},
	/**
	 * 调用错误接口
	 * @param object D DOM对象
	 * @param string txt 显示内容
	 * @return void
	 */
	error: function(D, txt) {
		this.init(D);
		D.dSuccess.style.display = "none";
		D.dError.style.display = "";
		D.dErrorText.nodeValue = txt;
	},
	/**
	 * 调用成功接口
	 * @param object D DOM对象
	 * @return void
	 */
	success: function(D) {
		this.init(D);
		D.dError.style.display = "none";
		D.dSuccess.style.display = "";
	},
	/**
	 * 清除提示接口
	 * @param object D DOM对象
	 * @return void
	 */
	clear: function(D) {
		this.init(D);
		D.dError.style.display = "none";
		D.dSuccess.style.display = "none";
	},
	/**
	 * 初始化错误对象
	 * @param object D DOM对象
	 * @return void
	 * @private
	 */
	_initError: function(D) {
		if (!D.dError || !D.dErrorText) {
			// 创建DOM结构
			var dFrag = document.createDocumentFragment();
			var dText = document.createTextNode("");
			var dB = document.createElement("b");
			var dSpan = document.createElement("span");
			var dDiv = document.createElement("div");
			// 组装HTML结构 - DIV
			D.dError = dFrag.appendChild(dSpan);
//			dDiv.className = "box-ver-new";
//			dDiv.style.display = "none";
			// 组装HTML结构 - SPAN
			//dDiv.appendChild( dSpan );
			// 组装HTML结构 - B
            dSpan.style.display = "none";
			dSpan.appendChild( dB );
            dSpan.className = "regboxErrorSpan";
			dB.className = "errorspan";
			D.dErrorText = dSpan.appendChild(dText);
			// 插入HTML
			var dParent = D.parentNode;
			var dNext = D.nextSibling;
			if(dNext) {
				dParent.insertBefore(dFrag, dNext);
			} else {
				dParent.appendChild(dFrag);
			}
		}
	},
	/**
	 * 初始化成功对象
	 * @param object D DOM对象
	 * @return void
	 * @private
	 */
	_initSuccess: function(D) {
		if(!D.dSuccess) {
			// 创建DOM结构
			var dFrag = document.createDocumentFragment();
			var dSpan = document.createElement("span");
			// 组装HTML结构 - SPAN
			D.dSuccess = dFrag.appendChild(dSpan);
			dSpan.className = "ico-ok";
			dSpan.style.display = "none";
			// 插入HTML
			var dParent = D.parentNode;
			var dNext = D.nextSibling;
			if(dNext) {
				dParent.insertBefore(dFrag, dNext);
			} else {
				dParent.appendChild(dFrag);
			}
		}
	}
};
// 定义Window属性
window.tips = tips;
})();


//这个可以验证15位和18位的身份证，并且包含生日和校验位的验证。   
//如果有兴趣，还可以加上身份证所在地的验证，就是前6位有些数字合法有些数字不合法。
function isIdCardNo(num) {
    var result = "";
    num = num.toUpperCase();
    //身份证号码为15位或者18位，15位时全为数字，18位前17位为数字，最后一位是校验位，可能为数字或字符X。
    var myReg15 = /^(\/d{15})|(\/d{17}([0-9]|X))$/;
    if (!(myReg15.test(num)))   
    { 
        result = '输入的身份证号格式不正确！'; 
    } 
    //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。 
    //下面分别分析出生日期和校验位 
    var len, re;
    len = num.length;
    if (len == 15) {
        re = new RegExp(/^(\/d{6})(\/d{2})(\/d{2})(\/d{2})(\/d{3})$/); 
        var arrSplit = num.match(re);

        //检查生日日期是否正确 
        var dtmBirth = new Date('19' + arrSplit[2] + '/' + arrSplit[3] + '/' + arrSplit[4]);
        var bGoodDay;
        bGoodDay = (dtmBirth.getYear() == Number(arrSplit[2])) && ((dtmBirth.getMonth() + 1) == Number(arrSplit[3])) && (dtmBirth.getDate() == Number(arrSplit[4]));
        if (!bGoodDay) {
            result = '输入的身份证号里出生日期不对！';
        }
        else {
            //将15位身份证转成18位 
            //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。 
            var arrInt = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            var arrCh = new Array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
            var nTemp = 0, i;
            num = num.substr(0, 6) + '19' + num.substr(6, num.length - 6);
            for (i = 0; i < 17; i++) {
                nTemp += num.substr(i, 1) * arrInt[i];
            }
            num += arrCh[nTemp % 11];
            result = "";
        }
    }
    if (len == 18) {
        //re = new RegExp(/^(\/d{6})(\/d{4})(\/d{2})(\/d{2})(\/d{3})([0-9]|X)$/);
        re = new RegExp(/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X)$/);
        var arrSplit = num.match(re);

        //检查生日日期是否正确 
        var dtmBirth = new Date(arrSplit[2] + "/" + arrSplit[3] + "/" + arrSplit[4]);
        var bGoodDay;
        bGoodDay = (dtmBirth.getFullYear() == Number(arrSplit[2])) && ((dtmBirth.getMonth() + 1) == Number(arrSplit[3])) && (dtmBirth.getDate() == Number(arrSplit[4]));
        if (!bGoodDay) {
            //alert(dtmBirth.getYear());
            //alert(arrSplit[2]);
            result = '输入的身份证号里出生日期不对！';
        }
        else {
            //检验18位身份证的校验码是否正确。 
            //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。 
            var valnum;
            var arrInt = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            var arrCh = new Array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
            var nTemp = 0, i;
            for (i = 0; i < 17; i++) {
                nTemp += num.substr(i, 1) * arrInt[i];
            }
            valnum = arrCh[nTemp % 11];
            if (valnum != num.substr(17, 1)) {
                //result = '18位身份证的校验码不正确！应该为：' + valnum;
                result = '输入的身份证号格式不正确';
            }
            else
                result = '';
        }
    }
    return result;
}

//验证手机号
function ismobile(str)
{
    var result = str.match(/^0*(13|15|18|17)\d{9}$/);
    if(result==null) return false;
    return true;
}

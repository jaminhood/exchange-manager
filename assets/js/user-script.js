let score = 0;
jQuery(document).ready(() => {
	/* === Utils === */
	jQuery(`.loader`).fadeOut(`slow`);

	$(() => {
		const sidebarNav = jQuery(".sidebar-nav");
		if(sidebarNav.length > 0) {
			$("#sidebarNav").metisMenu();
		}
	});

	$(".mobile-toggle").on("click", () => {
		$("body").toggleClass("sidebar-toggled");
	});

	$(() => {
		var scrollbar = jQuery(".scrollbar");
		if(scrollbar.length > 0) {
			$(".scroll_dark").mCustomScrollbar({
				theme: "minimal-dark",
				setHeight: false,
				mouseWheel: {
					normalizeDelta: true,
					scrollAmount: "200px",
					contentTouchScroll: true,
					deltaFactor: "200px",
				},
				advanced: {
					autoScrollOnFocus: "a[tabindex]",
				},
			});
			$(".scroll_light").mCustomScrollbar({
				theme: "minimal",
				setHeight: false,
				mouseWheel: {
					normalizeDelta: true,
					scrollAmount: "200px",
					contentTouchScroll: true,
					eltaFactor: "200px",
				},
				advanced: {
					autoScrollOnFocus: "a[tabindex]",
				},
			});
		}
	});

	$(() => {
		const select = $(".select-wrapper");
		if(select.length > 0) {
			$(".js-basic-single").select2();
			$(".bs-select-1").val();
			$(".bs-input").tagsinput("items");
		}
	});

	$(() => {
		if(score === 0) {
			$("#datatable-user").DataTable({
				bLengthChange: false,
				searching: false,
				bPaginate: true,
				bSortable: true,
			});
			$("#datatable-crypto").DataTable({
				bLengthChange: false,
				searching: false,
				bPaginate: true,
				bSortable: true,
			});
			score++;
		}
	});

	/* page */
	let page = ``;
	let exist = {};
	let assets;
	let passMatch = false;
	/* reset messages */
	const resetMsg = (dom) => {
		$(`.${dom}-error-msg`).html(``);
		$(`.${dom}-success-msg`).html(``);
	};
	/* set success message */
	const setSuccess = (dom, msg) => {
		resetMsg(dom);
		$(`.${dom}-success-msg`).html(msg);
	};
	/* set error message */
	const setError = (dom, msg) => {
		resetMsg(dom);
		$(`.${dom}-error-msg`).html(msg);
	};
	/* disable button */
	const disableBtn = (btn, disabledMsg = `Loading...`) => {
		btn.text(disabledMsg);
		btn.addClass(`disabled`);
		btn.attr(`disabled`, `disabled`);
	};
	/* enable button */
	const enableBtn = (btn, enableMsg = `...`) => {
		btn.text(enableMsg);
		btn.removeClass(`disabled`);
		btn.removeAttr(`disabled`);
	};
	/* check if value is number */
	const isNumberOrFloat = (number) =>
		/^[+-]?([0-9]+\.?[0-9]*|\.[0-9]+)$/.test(number);
	/* check if value is phone number */
	const isPhoneNumber = (number) => {
		const regex = new RegExp(
			"^[+]?[(]?[0-9]{3}[)]?[-s.]?[0-9]{3}[-s.]?[0-9]{4,8}$",
		);
		return regex.test(number);
	};
	/* check if value is email */
	const isEmail = (email) =>
		/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(
			email,
		);
	const isAlphaSpaceNum = (data) => /^[0-9A-Za-z\s\-]+$/.test(data);
	const isAlphaSpace = (data) => /^[A-Za-z\s\-]+$/.test(data);
	/* profile template */

	/* === Utils === */

	/* === Check user === */
	const checkUser = (field, value) => {
		return new Promise((resolve, reject) => {
			$.ajax({
				url: scriptData.ajaxurl,

				data: {
					action: `emCheckUser`,
					field: field,
					value: value,
				},

				success: (data) => {
					exist[field] = data[`data`];
					resolve();
				},

				error: (error) => {
					reject();
					window.alert(error);
				},
			});
		});
	};

	/* === Ajax === */
	/* Check user */
	const userCheck = (field, value) => {
		return new Promise((res, rej) => {
			$.ajax({
				url: scriptData.ajaxurl,
				data: {
					action: `emCheckUser`,
					field: field,
					value: value,
				},
				success: (data) => {
					res(data.data);
				},
				error: (error) => {
					rej();
					throw new Error(error);
				},
			});
		});
	};
	/* Check password */
	const passwordCheck = (user, pass) => {
		return new Promise((resolve, reject) => {
			$.ajax({
				url: scriptData.ajaxurl,
				data: {
					action: `emCheckPass`,
					username: user,
					password: pass,
				},
				success: (data) => {
					resolve(data[`data`]);
				},
				error: (error) => {
					reject();
					throw new Error(error);
				},
			});
		});
	};
	/* load Currency With Local Bank */
	const currencyWithLocalBank = (recipient) => {
		return new Promise((resolve, reject) => {
			$.ajax({
				url: script_data.ajaxurl, // The wordpress Ajax URL echoed on line 4
				data: {
					// The action is the WP function that'll handle this ajax request
					action: recipient,
				},
				success: (data) => {
					if(data.data.length > 0) {
						let instructions = "";
						let outputhtml = "";

						data.data.forEach((element) => {
							const bankName = element.bank.bank_name;
							const bankAccountName = element.bank.bank_account_name;
							const bankAccountNumber = element.bank.bank_account_number;

							instructions = `Send to this account number: Bank Name {${bankName}} | Account Name {${bankAccountName}} | Account Number {${bankAccountNumber}}`;
							instructions = instructions.replace("\\", "");
							outputhtml += `<option send="${instructions}" rate="${element.selling_price}" value="${element.id}">${element.name} | ${element.short_name}</option>`;
						});

						const assets = { data, instructions, outputhtml };
						resolve(assets);
					}
				},
				error: (errorThrown) => {
					reject();
					throw new Error(errorThrown);
				},
			});
		});
	};
	/* load Currency */
	const currency = (recipient, assetId) => {
		return new Promise((resolve, reject) => {
			$.ajax({
				url: script_data.ajaxurl, // The wordpress Ajax URL echoed on line 4
				data: {
					// The action is the WP function that'll handle this ajax request
					action: recipient,
				},
				success: (data) => {
					if(data.data.length > 0) {
						let instructions = "";
						let outputhtml = "";

						data.data.forEach((element) => {
							instructions =
								assetId == 1
									? element.sending_instruction
									: (instructions = element.wallet_address);

							instructions = instructions.replace("\\", "");
							outputhtml += `<option send="${instructions}" rate="${element.buying_price}" sell="${element.selling_price}" value="${element.id}">${element.name} | ${element.short_name}</option>`;
						});

						const asset = { data, instructions, outputhtml };
						resolve(asset);
					}
				},
				error: (errorThrown) => {
					reject();
					throw new Error(errorThrown);
				},
			});
		});
	};
	const localBankFn = (recipient) => {
		return new Promise((res, rej) =>
			$.ajax({
				url: scriptData.ajaxurl,
				data: { action: recipient },
				success: (data) => {
					const { bank_name, bank_account_name, bank_account_number } =
						data.data.bank;
					let outputString = `Deposit/Transfer the funds into this Nigerian local bank account,  Bank name: ${bank_name} | Bank Account Name: ${bank_account_name} | Bank Account Number: ${bank_account_number}`;
					res({ outputString, data });
				},
				error: (errorThrown) => {
					rej();
					throw new Error(errorThrown);
				},
			}),
		);
	};

	/* confirm username */
	const userConfirm = async (username) => {
		return await userCheck(`login`, username).then((res) => {
			exist.login = res;
			if(exist.login === 0) return false;
			return true;
		});
	};
	/* confirm email */
	const emailConfirm = async (email) => {
		return await userCheck(`email`, email).then((res) => {
			exist.email = res;
			if(exist.email === 0) return false;
			return true;
		});
	};
	/* confirm password */
	const passwordConfirm = async (username, password) => {
		return await passwordCheck(username, password).then((res) => {
			passMatch = res === 1 ? true : false;
			if(!passMatch) return false;
			return true;
		});
	};

	/* === templates === */
	/* login template */
	if($(`.loginTemplate`).length > 0) {
		/* page */
		page = `login`;
		/* form fields */
		const username = $(`#username`);
		const password = $(`#password`);
		const rmCheck = $(`#rememberMe`);
		const signInBtn = $(`#signInBtn`);
		let rememberMe = 0;
		/* disable unnecessary fields */
		password.attr(`disabled`, `disabled`);
		disableBtn(signInBtn, `Sign In`);
		/* username script */
		username.on(`input`, async (e) => {
			e.preventDefault();
			const userValue = username.val();
			if(userValue == ``) {
				resetMsg(`username`);
			} else {
				if(!(await userConfirm(userValue))) {
					setError(`username`, `User Invalid`);
					password.attr(`disabled`, `disabled`);
					return;
				} else {
					setSuccess(`username`, `User Valid`);
					password.removeAttr(`disabled`);
					return;
				}
			}
		});
		/* password script */
		password.on(`input`, async (e) => {
			e.preventDefault();
			const passValue = password.val();
			if(passValue == ``) {
				resetMsg(`password`);
			} else {
				if(!(await passwordConfirm(username.val(), passValue))) {
					setError(`password`, `Password do not match`);
					return;
				} else {
					setSuccess(`password`, `Password Matches`);
					enableBtn(signInBtn, `Sign In`);
					return;
				}
			}
		});
		/* submit script */
		$(`.loginTemplate`).submit((e) => {
			e.preventDefault();
			rememberMe = rmCheck.is(`checked`) ? 1 : 0;
			disableBtn(signInBtn, `Authenticating...`);
			$.ajax({
				url: scriptData.ajaxurl,
				data: {
					action: `emlogUserIn`,
					username: username.val(),
					password: password.val(),
					rememberMe: rememberMe,
				},
				success: (data) => {
					if(data[`data`] === 1) {
						window.location.href = scriptData.dashboardURL;
					} else {
						setError(`login`, `Error logging in`);
					}
					resolve();
				},
				error: (error) => {
					reject();
					throw new Error(error);
				},
			});
		});
	}
	/* register template */
	if($(`#customer-registration`).length > 0) {
		page = `register`;
		const button = $(`button[type="submit"]`);
		const regForm = $("#customer-registration");
		const firstName = $("#f-name");
		const lastName = $("#l-name");
		const phoneNumber = $("#phone-number");
		const email = $("#email");
		const uName = $("#username");
		const uPass = $("#password");

		let fName = firstName.val().trim();
		let lName = lastName.val().trim();
		let phone = phoneNumber.val().trim();
		let mail = email.val().trim();
		let user = uName.val().trim();
		let pass = uPass.val().trim();

		const errorContainers = [`firstname`, `lastname`, `phone`, `email`, `username`, `password`]

		let registeration_form = document.getElementById("customer-registration");
		let first_name = document.getElementById("f-name");
		let last_name = document.getElementById("l-name");
		let phone_number = document.getElementById("phone-number");
		let username = document.getElementById("username");
		let password = document.getElementById("password");
		let result = true;

		const checkInputs = async () => {
			fName = firstName.val().trim();
			lName = lastName.val().trim();
			phone = phoneNumber.val().trim();
			mail = email.val().trim();
			user = uName.val().trim();
			pass = uPass.val().trim();
			const checks = {
				fCheck: true,
				lCheck: true,
				pCheck: true,
				eCheck: true,
				uCheck: true,
				pwCheck: true,
			};

			if(fName.length < 3) {
				setError(`firstname`, `Minumum of 4 Characters Required`);
				checks.fCheck = false;
			} else {
				setSuccess(`firstname`, `Firstname is valid`);
				checks.fCheck = true;
			}

			if(lName.length < 3) {
				setError(`lastname`, `Minumum of 4 Characters Required`);
				checks.lCheck = false;
			} else {
				setSuccess(`lastname`, `Lastname is valid`);
				checks.lCheck = true;
			}

			if(phone === "") {
				setError(`phone`, `Phone Number Cannot be empty`);
				checks.pCheck = false;
			} else if(!isPhoneNumber(phone)) {
				setError(`phone`, `Invalid Phone Number`);
				checks.pCheck = false;
			} else {
				setSuccess(`phone`, `Phone number is valid`);
				checks.pCheck = true;
			}

			if(mail === "") {
				setError(`email`, `Email Address cannot be empty`);
				checks.eCheck = false;
			} else if(!isEmail(mail)) {
				setError(`email`, `Invalid eMail Address`);
				checks.eCheck = false;
			} else if(await emailConfirm(mail)) {
				setError(`email`, `This eMail have been used by someone else`);
				checks.eCheck = false;
			} else {
				setSuccess(`email`, `Email address is valid`);
				checks.eCheck = true;
			}

			if(user.length < 6) {
				setError(`username`, `Minumum of 6 Characters Required`);
				checks.uCheck = false;
			} else if(await userConfirm(user)) {
				setError(`username`, `Username Already In Use`);
				checks.uCheck = false;
			} else {
				setSuccess(`username`, `Username is valid`);
				checks.uCheck = true;
			}

			if(pass.length < 6) {
				setError(`password`, `Minumum of 6 Characters Required`);
				checks.pwCheck = false;
			} else {
				setSuccess(`password`, `Password is valid`);
				checks.pwCheck = true;
			}

			result =
				checks.fCheck &&
				checks.lCheck &&
				checks.pCheck &&
				checks.eCheck &&
				checks.uCheck &&
				checks.pwCheck;

			return result;
		};

		const createAccount = () => {
			return new Promise((resolve, reject) => {
				fName = firstName.val().trim();
				lName = lastName.val().trim();
				phone = phoneNumber.val().trim();
				mail = email.val().trim();
				user = uName.val().trim();
				pass = uPass.val().trim();
				data = {
					first_name: fName,
					last_name: lName,
					phone_number: phone,
					email: mail,
					username: user,
					password: pass,
				};

				$.ajax({
					url: scriptData.ajaxurl,

					data: {
						action: "hid_ex_m_complete_user_registration",
						data: data,
					},

					success: () => {
						resolve(`success`);
					},
					error: (errorThrown) => {
						reject();
						throw new Error(errorThrown);
					},
				});
			});
		}

		regForm.on(`submit`, async (e) => {
			errorContainers.forEach(error => resetMsg(error))
			disableBtn(button, `Please wait...`);
			await checkInputs().then((res) => {
				if(!res) {
					enableBtn(button, `Sign Up`);
					return false
				}
				return true
			}).then(async (res) => {
				if(res) {
					await createAccount().then(data => {
						if(data === `success`) {
							disableBtn(button, `Registration Successful`);
							setTimeout(() => window.location.href = scriptData.signInURL, 500);
						}
					})
				}
			})
		});

		registeration_form.addEventListener("submit", function (e) {
			e.preventDefault();
			verify_user_existence(email.value.trim(), username.value.trim());
		});

		function registration_success() {
			window.location.href = script_data.sign_in_url;
		}
	}
	/* buy template */
	if($(`#buyAsset`).length > 0) {
		assets = {};
		const button = $(`button[type="submit"]`);
		const buyForm = $(`#buyAsset`);
		const ecurrencyAsset = $(`#eCurrency`);
		const assetsSelectMenu = $(`#selectAsset`);
		const cryptoAsset = $(`#cryptoCurrency`);
		const rateOutput = $(`.form-exchange-rate`);
		const qtyElement = $(`#quantity`);
		const qtyWrapper = $(`.qtyWrapper`);
		const selectWrapper = $(`.select-wrapper`);
		const feeElement = $(`#fee`);
		const recieving = $(`#receivingInstructions`);
		const sending = $(`#sendingInstructions`);
		const imageInput = $(".proofImg");
		let selectedRate = 0;
		let assetNumber = 0;
		qtyWrapper.fadeOut();
		selectWrapper.fadeOut();
		resetMsg(`buying`);

		(async () => {
			await currencyWithLocalBank(`hid_ex_m_get_e_assets_with_local_bank`).then(
				(data) => (assets.eCurrency = data),
			);
			await currencyWithLocalBank(
				`hid_ex_m_get_crypto_assets_with_local_bank`,
			).then((data) => (assets.crypto = data));
		})();

		const assetDom = (curr) => {
			const data = curr.data.data[0];
			selectedRate = data.selling_price;
			assetsSelectMenu.html(curr.outputhtml);
			rateOutput.html(selectedRate);
			const bankName = data.bank.bank_name;
			const accountName = data.bank.bank_account_name;
			const accountNumber = data.bank.bank_account_number;
			const instructions = `
				Send to this account number: Bank Name {${bankName}} |
				Account Name {${accountName}} | Account Number {${accountNumber}}
			`;
			sending.html(instructions);
			qtyWrapper.fadeIn();
			selectWrapper.fadeIn();
			resetMsg(`buying`);
		};

		ecurrencyAsset.on(`click`, () => {
			assetNumber = 1;
			const curr = assets.eCurrency;
			assetDom(curr);
		});

		cryptoAsset.on(`click`, () => {
			assetNumber = 2;
			const curr = assets.crypto;
			assetDom(curr);
		});

		assetsSelectMenu.on(`change`, () => {
			const send = $(`#selectAsset option:selected`).attr(`send`);
			selectedRate = $(`#selectAsset option:selected`).attr(`rate`);
			rateOutput.html(selectedRate);
			$("#sendingInstructions").html(send);
		});

		qtyElement.on(`input`, () => {
			resetMsg(`buying`);
			if(!isNumberOrFloat(qtyElement.val())) {
				qtyElement.val(qtyElement.val().slice(0, -1));
			} else {
				const fee_ = qtyElement.val() * selectedRate;
				feeElement.val(fee_.toFixed(2));
			}
		});

		const isRecieving = (data) => /^[0-9A-Za-z\s\-]+$/.test(data);
		recieving.on("input", () => {
			if(!isRecieving(recieving.val()))
				recieving.val(recieving.val().slice(0, -1));
		});

		imageInput.change((e) => {
			e.preventDefault();
			resetMsg(`buying`);
			$(".image-label").text(imageInput.val().replace(/C:\\fakepath\\/i, ""));
		});

		buyForm.on("submit", (e) => {
			e.preventDefault();
			disableBtn(button, `Loading...`);
			const formData = new FormData(e.target);
			let errorMsg = "";
			const checks = {
				asset: true,
				qty: true,
				proof: true,
				receive: true,
			};

			if(assetsSelectMenu.val() == 0) {
				checks.asset = false;
				errorMsg += `No Assets Selected<br>`;
			}

			if(qtyElement.val() == 0 || qtyElement.val() == "") {
				checks.qty = false;
				errorMsg += "Empty Quantity<br>";
			}

			if(!imageInput.val()) {
				checks.proof = false;
				errorMsg += "No Image Selected<br>";
			}

			if(recieving.val().length < 6) {
				checks.receive = false;
				errorMsg += "Invalid Recieving Instructions<br>";
			}

			if(checks.asset && checks.qty && checks.proof && checks.receive) {
				const price = selectedRate * qtyElement.val();

				formData.append("file", imageInput[0].files[0]);
				formData.append("amount_to_recieve", price.toFixed(2));
				formData.append("chosen_asset_type", assetNumber);
				formData.append("sending", recieving.val());
				formData.append("chosen_asset_id", assetsSelectMenu.val());
				formData.append("entered_quantity", qtyElement.val());
				formData.append("action", "hid_ex_m_submit_buy_order");
				formData.append("security", scriptData.security);

				$.ajax({
					type: "POST",
					url: scriptData.ajaxurl,
					data: formData,
					dataType: "json",
					contentType: false,
					processData: false,
					success: (data) => {
						if(data.data == 1) {
							setSuccess(`buying`, `Order Successful`);
							disableBtn(button, `Order Successful`);
							setInterval(() => location.reload(), 3000);
						} else if(data.data == 0) {
							setError(`buying`, `Order Unsuccessful`);
							enableBtn(button, `Buy Now`);
						}
					},
					error: (errorThrown) => {
						throw new Error(errorThrown);
					},
				});
			} else {
				setError(`buying`, errorMsg);
				enableBtn(button, `Buy Now`);
			}
		});
	}
	/* sell template */
	if($(`#sellAsset`).length > 0) {
		assets = {};
		const button = $(`button[type="submit"]`);
		const sellForm = $(`#sellAsset`);
		const ecurrencyAsset = $(`#eCurrency`);
		const cryptoAsset = $(`#cryptoCurrency`);
		const assetsSelectMenu = $(`#selectAsset`);
		const rateOutput = $(`.form-exchange-rate`);
		const sending = $(`#sendingInstructions`);
		const qtyElement = $(`#quantity`);
		const qtyWrapper = $(`.qtyWrapper`);
		const selectWrapper = $(`.select-wrapper`);
		const feeElement = $(`#fee`);
		const imageInput = $(`.proofImg`);
		let assetNumber = 0;
		let selectedRate = 0;
		qtyWrapper.fadeOut();
		selectWrapper.fadeOut();
		resetMsg(`selling`);

		(async () => {
			await currency(`hid_ex_m_get_e_assets`, 1).then(
				(data) => (assets.eCurrency = data),
			);
			await currency(`hid_ex_m_get_crypto_assets`, 2).then(
				(data) => (assets.crypto = data),
			);
		})();

		const assetDom = (curr) => {
			const data = curr.data.data[0];
			selectedRate = data.buying_price;
			assetsSelectMenu.html(curr.outputhtml);
			rateOutput.html(selectedRate);
			sending.html(curr.instructions);
			qtyWrapper.fadeIn();
			selectWrapper.fadeIn();
			resetMsg(`selling`);
		};

		ecurrencyAsset.on(`click`, () => {
			assetNumber = 1;
			const curr = assets.eCurrency;
			assetDom(curr);
		});

		cryptoAsset.on(`click`, () => {
			assetNumber = 2;
			const curr = assets.crypto;
			assetDom(curr);
		});

		assetsSelectMenu.on("change", () => {
			selectedRate = $(`#selectAsset option:selected`).attr(`rate`);
			const send = $(`#selectAsset option:selected`).attr(`send`);
			rateOutput.html(selectedRate);
			sending.html(send);
		});

		qtyElement.on("input", () => {
			resetMsg(`selling`);
			if(qtyElement.val() == ``) {
				feeElement.val(``);
				return;
			}
			if(!isNumberOrFloat(qtyElement.val())) {
				qtyElement.val(qtyElement.val().slice(0, -1));
			} else {
				let fee_ = qtyElement.val() * selectedRate;
				feeElement.val(fee_.toFixed(2));
			}
		});

		imageInput.change((e) => {
			e.preventDefault();
			resetMsg(`selling`);
			$(".image-label").text(imageInput.val().replace(/C:\\fakepath\\/i, ""));
		});

		sellForm.on("submit", (e) => {
			e.preventDefault();
			disableBtn(button, `Loading...`);
			const formData = new FormData(e.target);
			let errorMsg = ``;
			const checks = {
				asset: true,
				qty: true,
				proof: true,
			};

			if(assetsSelectMenu.val() == 0) {
				checks.asset = false;
				errorMsg += "No Assets Selected<br>";
			}

			if(qtyElement.val() == 0 || qtyElement.val() == "") {
				checks.qty = false;
				errorMsg += "Empty Quantity<br>";
			}

			if(!imageInput.val()) {
				checks.proof = false;
				errorMsg += "No Image Selected<br>";
			}

			if(checks.asset && checks.qty && checks.proof) {
				let price = selectedRate * qtyElement.val();

				formData.append("file", imageInput[0].files[0]);
				formData.append("amount_to_recieve", price.toFixed(2));
				formData.append("chosen_asset_type", assetNumber);
				formData.append("chosen_asset_id", assetsSelectMenu.val());
				formData.append("entered_quantity", qtyElement.val());
				formData.append("action", "hid_ex_m_submit_sell_order");
				formData.append("security", scriptData.security);

				$.ajax({
					type: "POST",
					url: scriptData.ajaxurl,
					data: formData,
					dataType: "json",
					contentType: false,
					processData: false,

					success: (data) => {
						if(data.data == 1) {
							setSuccess(`selling`, `Order Successful`);
							disableBtn(button, `Order Successful`);
							setInterval(() => location.reload(), 3000);
						} else {
							setError(`selling`, `Order Unsuccessful`);
							enableBtn(button, `Sell Now`);
						}
					},
					error: (errorThrown) => {
						throw new Error(errorThrown);
					},
				});
			} else {
				setError(`selling`, errorMsg);
				enableBtn(button, `Sell Now`);
			}
		});
	}
	/* rates calculator */
	if($(`#ratesCalculator`).length > 0) {
		assets = {};
		const ecurrencyAsset = $(`#eCurrency`);
		const cryptoAsset = $(`#cryptoCurrency`);
		const assetsSelectMenu = $(`#selectAsset`);
		const outBuy = $("#output-buying");
		const outSell = $("#output-selling");
		const outBuyQ = $("#output-buying-q");
		const outSellQ = $("#output-selling-q");
		const qtyElement = $("#item-quantity");
		const qtyWrapper = $(`.qtyWrapper`);
		let buyingPrice = 0;
		let sellingPrice = 0;
		qtyWrapper.fadeOut();

		(async () => {
			await currency(`hid_ex_m_get_e_assets`, 1).then(
				(data) => (assets.eCurrency = data),
			);
			await currency(`hid_ex_m_get_crypto_assets`, 2).then(
				(data) => (assets.crypto = data),
			);
		})();

		const assetDom = (curr) => {
			const data = curr.data.data[0];
			buyingPrice = data.buying_price;
			sellingPrice = data.selling_price;
			assetsSelectMenu.html(curr.outputhtml);

			if(qtyElement.val() != ``) {
				outBuyQ.val(qtyElement.val() * buyingPrice);
				outSellQ.val(qtyElement.val() * sellingPrice);
			} else {
				outBuy.val(buyingPrice);
				outSell.val(sellingPrice);
			}

			qtyWrapper.fadeIn();
		};

		ecurrencyAsset.on(`click`, () => {
			const curr = assets.eCurrency;
			assetDom(curr);
		});

		cryptoAsset.on(`click`, () => {
			const curr = assets.crypto;
			assetDom(curr);
		});

		assetsSelectMenu.on("change", () => {
			buyingPrice = $(`#selectAsset option:selected`).attr(`rate`);
			sellingPrice = $(`#selectAsset option:selected`).attr(`sell`);

			if(qtyElement.val() != ``) {
				outBuyQ.val(qtyElement.val() * buyingPrice);
				outSellQ.val(qtyElement.val() * sellingPrice);
			} else {
				outBuy.val(buyingPrice);
				outSell.val(sellingPrice);
			}
		});

		qtyElement.on("input", () => {
			if(qtyElement.val() == ``) {
				outBuyQ.val(``);
				outSellQ.val(``);
				return;
			}
			if(!isNumberOrFloat(qtyElement.val())) {
				qtyElement.val(qtyElement.val().slice(0, -1));
			} else {
				outBuyQ.val(qtyElement.val() * buyingPrice);
				outSellQ.val(qtyElement.val() * sellingPrice);
			}
		});
	}
	/* profile template */
	if($(`#userProfileForm`).length > 0) {
		const button = $(`button[type="submit"]`);
		const firstName = $(`#firstName`);
		const lastName = $(`#lastName`);
		const phone = $(`#phoneNumber`);
		const email = $(`#email`);
		const password = $(`#password`);
		const rePassword = $(`#retypePassword`);
		const profileForm = $(`#userProfileForm`);

		console.log(button);

		firstName.on(`input`, () => {
			if(!isAlphaSpace(firstName.val())) {
				firstName.val(firstName.val().slice(0, -1));
			}
		});

		lastName.on(`input`, () => {
			if(!isAlphaSpace(lastName.val())) {
				lastName.val(lastName.val().slice(0, -1));
			}
		});

		phone.on(`input`, () => {
			if(!isPhoneNumber(phone.val())) {
				phone.val(phone.val().slice(0, -1));
			}
		});

		email.on(`blur`, () => {
			if(!isEmail(email.val())) {
				email.val(``);
			}
		});

		profileForm.on(`submit`, (e) => {
			e.preventDefault();
			const event = e.target;
			disableBtn(button, `Updating ...`);

			const formData = new FormData(event);

			if(!(password.val() == rePassword.val())) {
				setError(`update`, `Password MisMatch`);
				enableBtn(button, `Update Information`);
				return;
			}

			formData.append(`username`, $(`#username`).val());
			formData.append(`action`, `hid_ex_m_update_customer`);

			$.ajax({
				type: `POST`,
				url: scriptData.ajaxurl,
				data: formData,
				dataType: "json",
				contentType: false,
				processData: false, //this is a must

				success: (data) => {
					if(data.data == 0) {
						setError(`update`, `An error occured, please try again`);
						enableBtn(button, `Update Information`);
					} else if(data.data == 1) {
						setSuccess(
							`update`,
							`User Profile Updated Successfully.<br>Refreshing Profile Details...`,
						);
						disableBtn(button, `Update Successful`);
						setTimeout(() => location.reload(), 3000);
					}
				},
				error: (errorThrown) => {
					throw new Error(errorThrown);
				},
			});
		});
	}
	/* support template */
	if($(`#ticket-form`).length > 0) {
		const button = $(`button[type="submit"]`);
		const createTicket = $(`#ticket-form`);
		const formTitle = $(`#ticket-form #title`);
		const formBody = $(`#ticket-form #details`);

		formTitle.on("input", () => {
			if(!isAlphaSpaceNum(formTitle.val()))
				formTitle.val(formTitle.val().slice(0, -1));
		});

		formBody.on("input", () => {
			if(!isAlphaSpaceNum(formBody.val()))
				formBody.val(formBody.val().slice(0, -1));
		});

		createTicket.on(`submit`, (e) => {
			e.preventDefault();
			const event = e.target;
			const ticketData = new FormData(event);
			disableBtn(button, `Openning ticket...`);

			let errorMsg = "";
			const checks = {
				title: true,
				body: true,
			};

			if(formTitle.val().length < 5) {
				checks.title = false;
				errorMsg += "Title requires a minimum of 5 Characters<br>";
			}

			if(formBody.val().length < 10) {
				checks.body = false;
				errorMsg += "Ticket's Description requires a minimum of 10 Characters";
			}

			if(!(checks.title && checks.body)) {
				setError(`form`, errorMsg);
				enableBtn(button, `Open Ticket`);
				return;
			}

			ticketData.append("action", "hid_ex_m_customer_open_new_support_ticket");

			$.ajax({
				type: "POST",
				url: scriptData.ajaxurl,
				data: ticketData,
				dataType: "json",
				contentType: false,
				processData: false,
				success: (data) => {
					if(data.data == 0) {
						setError(`form`, `Ticket Submission Unsuccessful`);
						enableBtn(button, `Open Ticket`);
					} else if(data.data == 1) {
						setSuccess(
							`form`,
							`Ticket Submission Successful.<br>Refreshing ...`,
						);
						setTimeout(() => location.reload(), 3000);
					}
				},
				error: (errorThrown) => {
					throw new Error(errorThrown);
				},
			});
		});

		const chatArea = $(`.chat-box`);
		const chatTitle = $(`.chat-title`);
		const sendChatForm = $(`#send-chat`);
		let timer = 0;

		const chatBtns = $(`.open-chat`);

		for(btn of chatBtns) {
			const ticketID = btn.getAttribute(`ticket`);
			const ticketTitle = btn.getAttribute(`ticketTitle`);
			btn.addEventListener(`click`, (e) => {
				clearInterval(timer)
				$(`.attachment-text`).text(``);
				e.preventDefault();
				chatArea.html(`
					<div class="d-flex justify-content-center align-items-center" style="height: 70vh;">
						<h2>Loading...</h2>
					</div>
				`)
				$(`.chat-div`).fadeOut()
				chatTitle.html(ticketTitle);
				sendChatForm.attr(`ticketid`, ticketID);
				idData = new FormData();
				idData.append("ticket-id", ticketID);
				idData.append("action", "hid_ex_m_retrieve_ticket_chats");
				timer = setInterval(async () => await getChat(idData).then(dom => {
					chatArea.html(`
					<div class="d-flex justify-content-center align-items-center" style="height: 70vh;">
						<h2>Loading...</h2>
					</div>`).delay(500).html(dom)
					$(`.chat-div`).fadeIn()
				}), 500);
			});
		}

		function getChat(idData) {
			return new Promise((resolve, reject) =>
				$.ajax({
					type: "POST",
					url: scriptData.ajaxurl,
					data: idData,
					dataType: "json",
					contentType: false,
					processData: false,

					success: (data) => {
						if(data.data.length == 0) {
							resolve(`
							<p class='text-center'>
								No Chats Available on this ticket
							</p>
						`);
						} else {
							let buildString = ``;
							data.data.forEach((item) => {
								const {
									sender,
									message,
									attachment,
									attachment_url,
									time_stamp,
								} = item;
								const el = {
									clsName:
										sender == `Admin`
											? `chat`
											: `chat chat-left justify-content-end`,
									msg: message != `` ? message : ``,
									attachment:
										attachment != 0
											? `<a href=${attachment_url} target="_blank">View attachment</a></br>`
											: ``,
									time: time_stamp,
								};
								buildString += `
									<div class="${el.clsName}">
										<div class="chat-msg">
											<div class="chat-msg-content">
												<p>${el.msg}</p>
												${el.attachment}
												<p class="chat-time">${el.time}</p>
											</div>
										</div>
									</div>
								`;
							});

							resolve(buildString)
						}
					},
					error: (errorThrown) => {
						reject()
						throw new Error(errorThrown);
					},
				}))
		}

		const attachment = $(`#send-chat .attachment`);

		const attachmentImg = $(`#attachmentImg`);

		attachment.on(`click`, (e) => {
			e.preventDefault();
			const ticketID = sendChatForm.attr(`ticketid`);
			ticketID
				? attachmentImg.trigger("click")
				: $(`.attachment-text`).text("No chat yet");
		});

		attachmentImg.change(function (e) {
			e.preventDefault();
			let outputName = "";
			outputName = this.files[0].name;
			if(this.files[0].name.length > 40) {
				outputName = "..." + this.files[0].name.slice(-40);
			}
			$(`.attachment-text`).text(outputName);
		});

		sendChatForm.on(`submit`, (e) => e.preventDefault());

		const sendChat = $(`#send-chat .send-btn`);

		sendChat.on(`click`, (e) => {
			e.preventDefault();
			const ticketID = sendChatForm.attr(`ticketid`);
			if(ticketID) {
				const chatText = $(`#send-chat .chat-text`);
				const sender = $(`#send-chat #sender`);
				let text = chatText.val();

				if(text.length === 0 && attachmentImg[0].files[0] === undefined) {
					$(`.attachment-text`).text("No Texts entered - No Image selected");
					return;
				}

				let formData = new FormData();
				formData.append("ticket", ticketID);
				formData.append("new-chat-text", text);
				formData.append("sender", sender.val());
				formData.append("file", attachmentImg[0].files[0]);
				formData.append("action", "hid_ex_m_create_a_new_chat");
				formData.append("security", scriptData.security);

				$.ajax({
					type: "POST",
					url: scriptData.ajaxurl,
					data: formData,
					dataType: "json",
					contentType: false,
					processData: false,

					success: (data) => {
						if(data["data"] == -1) {
							$(`.attachment-text`).text(`Error Sending message`);
						} else {
							attachmentImg.val(``);
							chatText.val(``);
							$(`.attachment-text`).text(``);
						}
					},
					error: (errorThrown) => {
						throw new Error(errorThrown);
					},
				});
			} else {
				$(`.attachment-text`).text("No chat yet");
			}
		});
	}
	/* wallet template */
	if($(`#fundModal`).length > 0) {
		assets = {};
		const button = $(`button[type="submit"]`);
		const fundForm = $(`#fundModal`);
		const eCurrency = $(`#fundModal #eCurrency`);
		const cryptoCurrency = $(`#fundModal #cryptoCurrency`);
		const localBank = $(`#fundModal #localBank`);
		const selectElement = $("#fundModal #selected-asset");
		const rateOutput = $("#fundModal .form-exchange-rate");
		const sending = $("#fundModal #sendingInstructions");
		const amountOutput = $("#fundModal #amount-output");
		const qtyInput = $("#fundModal #qty");
		let assetTypeIndicator = ``;
		let assetIndicator = 0;
		let rate = 0;

		(async () => {
			await currency(`hid_ex_m_get_e_assets`, 1).then(
				(data) => (assets.eCurrency = data),
			);
			await currency(`hid_ex_m_get_crypto_assets`, 2).then(
				(data) => (assets.crypto = data),
			);
			await localBankFn(`hid_ex_m_get_wallet_funding_local_bank`).then(
				(data) => (assets.localBank = data),
			);
		})();

		const assetDom = (buying_price, outputhtml, instructions) => {
			$(`.select-wrapper`).fadeIn();
			$(`.qty-wrapper`).fadeIn();
			$(`.rate-wrapper`).fadeIn();
			$(`#amount-output`).attr(`disabled`, `disabled`);
			rate = buying_price;
			selectElement.html(outputhtml);
			rateOutput.text(rate);
			sending.html(instructions);
		};

		eCurrency.on(`click`, () => {
			assetIndicator = 1;
			const curr = assets.eCurrency;
			const data = curr.data.data[0];
			const { buying_price, short_name } = data;
			assetDom(buying_price, curr.outputhtml, curr.instructions);
			assetTypeIndicator = `eCurr || ${short_name}`;
		});

		cryptoCurrency.on(`click`, () => {
			assetIndicator = 2;
			const curr = assets.crypto;
			const data = curr.data.data[0];
			const { buying_price, short_name } = data;
			assetDom(buying_price, curr.outputhtml, curr.instructions);
			assetTypeIndicator = `Crypto || ${short_name}`;
		});

		localBank.on(`click`, () => {
			const curr = assets.localBank;
			$(`.select-wrapper`).fadeOut();
			$(`.qty-wrapper`).fadeOut();
			$(`.rate-wrapper`).fadeOut();
			$(`#amount-output`).removeAttr(`disabled`);
			sending.html(curr.outputString);
		});

		selectElement.on("change", () => {
			rate = $(`#fundModal #selected-asset option:selected`).attr(`rate`);
			let send = $(`#fundModal #selected-asset option:selected`).attr(`send`);
			let short = $(`#fundModal #selected-asset option:selected`).attr(`short`);
			rateOutput.html(rate);
			sending.html(send);
			const fee_ = qtyInput.val() * rate;
			amountOutput.val(fee_.toFixed(2));
			assetIndicator == 1
				? (assetTypeIndicator = `Funding || eCurr - ${short}`)
				: (assetTypeIndicator = `Funding || Crypto - ${short}`);
		});

		qtyInput.on("input", () => {
			if(!isNumberOrFloat(qtyInput.val())) {
				qtyInput.val(qtyInput.val().slice(0, -1));
			} else {
				let fee_ = qtyInput.val() * rate;
				amountOutput.val(fee_.toFixed(2));
			}
		});

		$("input[type='file']").change(function (e) {
			e.preventDefault();
			$(".custom-file-label").text(this.value.replace(/C:\\fakepath\\/i, ""));
		});

		fundForm.on("submit", (ev) => {
			disableBtn(button, `Please Wait...`);
			ev.preventDefault();
			resetMsg(`fund`);
			const target = ev.target;
			const formdata = new FormData(target);
			let message = "";

			if(!formdata.get("mode")) {
				setError(`fund`, "No mode selected");
				enableBtn(button, `Fund Wallet`);
				return;
			}

			if(formdata.get("mode") == 0) {
				if(amountOutput.val() == 0 || amountOutput.val() == "") {
					message += "Invalid Amount";
				}

				if(!$("input[type='file']").val()) {
					message += "<br>Missing Proof of Payment";
				}

				if(message) {
					setError(`fund`, message);
					enableBtn(button, `Fund Wallet`);
					message = "";
					return;
				}

				formdata.append("amount", amountOutput.val());
				formdata.append("details", "Funding || Local Bank Payment");
			} else {
				if(qtyInput.val() == 0 || qtyInput.val() == "") {
					message += "Invalid Quantity";
				}

				if(!$("input[type='file']").val()) {
					message += "<br>Missing Proof of Payment";
				}

				if(message) {
					setError(`fund`, message);
					enableBtn(button, `Fund Wallet`);
					message = "";
					return;
				}

				let fee_ = qtyInput.val() * rate;

				let detailsTemp = `${assetTypeIndicator} || Payment rate = ${rate}`;

				formdata.append("amount", fee_.toFixed(2));
				formdata.append("details", detailsTemp);
			}

			formdata.append("file", $("input[type='file']").prop(`files`)[0]);
			formdata.append("action", "hid_ex_m_credit_wallet");
			formdata.append("security", scriptData.security);

			$.ajax({
				type: "POST",
				url: scriptData.ajaxurl,
				data: formdata,
				dataType: "json",
				contentType: false,
				processData: false,
				success: (data) => {
					if(data.data == 1) {
						setSuccess(
							`fund`,
							"Transaction submitted successfully. Reloading...",
						);
						disableBtn(button, `Transaction Successful`);
						setInterval(() => location.reload(), 2000);
					} else {
						setError(`fund`, "An unknown error occured");
						enableBtn(button, `Fund Wallet`);
					}
				},
				error: (errorThrown) => {
					throw new Error(errorThrown);
				},
			});
		});
	}
	if($(`#withdrawalModal`).length > 0) {
		const button = $(`button[type="submit"]`);
		const withdrawForm = $(`#withdrawalModal`);
		const amountInput = $(`#withdrawalModal #amount`);
		const sending = $(`#withdrawalModal #sendingInstructions`);

		withdrawForm.on("submit", function (ev) {
			ev.preventDefault();
			disableBtn(button, `Please Wait...`);
			resetMsg(`withdraw`);

			const target = ev.target;
			const formdata = new FormData(target);
			let message = "";

			if(!formdata.get("mode_w")) {
				setError(`withdraw`, "No mode selected");
				enableBtn(button, `Withdraw from Wallet`);
				return;
			}

			if(amountInput.val() == 0 || amountInput.val() == "")
				message += "Invalid Amount";

			if(amountInput.val() < 500)
				message += "<br>Minimum Amount You can Withdraw is #500";

			const sendin = sending.val();

			if(sendin.length < 10)
				message +=
					"<br>Sending Instruction requires a minimum of 10 characters";

			if(message) {
				setError(`withdraw`, message);
				enableBtn(button, `Withdraw from Wallet`);
				message = "";
				return;
			}

			const detailsTemp = `Withdrawal || Local Bank Payment || Instruction - ${sendin}`;

			formdata.append("amount_", amountInput.val());
			formdata.append("details", detailsTemp);
			formdata.append("info", sendin);
			formdata.append("action", "hid_ex_m_debit_wallet");

			$.ajax({
				type: "POST",
				url: scriptData.ajaxurl,
				data: formdata,
				dataType: "json",
				contentType: false,
				processData: false,
				success: (data) => {
					if(data.data == 1) {
						setSuccess(
							`withdraw`,
							"Transaction submitted successfully. Reloading...",
						);
						disableBtn(button, `Transaction successful`);
						setInterval(() => location.reload(), 2000);
					} else if(data["data"] == 0) {
						setError(`withdraw`, `Insufficient Balance`);
						enableBtn(button, `Withdraw from Wallet`);
					} else {
						setError(`withdraw`, `An Unknown Error Occured`);
						enableBtn(button, `Withdraw from Wallet`);
					}
				},
				error: (errorThrown) => {
					throw new Error(errorThrown);
				},
			});
		});
	}
});

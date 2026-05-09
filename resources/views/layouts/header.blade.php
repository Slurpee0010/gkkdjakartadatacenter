<!DOCTYPE html>
<html class="no-js" lang="zxx">
	<head>
		<!-- Meta Tags -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="keywords" content="Site keywords here">
		<meta name="description" content="#">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<!-- Site Title -->
		<title>NFT MAX - NFT Dashboard Template</title>
		
		<!-- Fav Icon -->
		<link rel="icon" href="assets/img/favicon.png">
		

		<!-- NFTMax Stylesheet -->
		<link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">
		<link rel="stylesheet" href="{{asset('assets/css/font-awesome-all.min.css')}}">
		<link rel="stylesheet" href="{{asset('assets/css/charts.min.css')}}">
		<link rel="stylesheet" href="{{asset('assets/css/slickslider.min.css')}}">
		<link rel="stylesheet" href="{{asset('assets/css/reset.css')}}">
		<link rel="stylesheet" href="{{asset('assets/style.css')}}">
		
	</head>
	<body>
	
		<div class="body-bg" style="background-image:url('assets/img/body-bg.jpg')">
		
			<!-- NFTMax Admin Menu -->
			<div class="nftmax-smenu">
				<!-- Admin Menu -->
				<div class="admin-menu">
					<!-- Logo -->
					<div class="logo">
						<a href="{{ url('/') }}">
							<img class="nftmax-logo__main" src="assets/img/gkkd-yellow.png" alt="#">
						</a>
						<div class="nftmax__sicon close-icon"><img src="assets/img/menu-toggle.svg" alt="#"></div>
					</div>
					<!-- Author Details -->
					<div class="admin-menu__one">
						<h4 class="admin-menu__title nftmax-scolor">Menu</h4>
						<!-- Nav Menu -->
						<div class="menu-bar">
							<ul class="menu-bar__one">
								<li class="active"><a href="{{url('/')}}"><span class="menu-bar__text"><span class="nftmax-menu-icon nftmax-svg-icon__v1"><svg class="nftmax-svg-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path d="M0.800781 2.60005V7.40005H7.40078V0.800049H2.60078C2.12339 0.800049 1.66555 0.989691 1.32799 1.32726C0.990424 1.66482 0.800781 2.12266 0.800781 2.60005H0.800781Z"></path><path d="M13.4016 0.800049H8.60156V7.40005H15.2016V2.60005C15.2016 2.12266 15.0119 1.66482 14.6744 1.32726C14.3368 0.989691 13.879 0.800049 13.4016 0.800049V0.800049Z"></path><path d="M0.800781 13.4001C0.800781 13.8775 0.990424 14.3353 1.32799 14.6729C1.66555 15.0105 2.12339 15.2001 2.60078 15.2001H7.40078V8.6001H0.800781V13.4001Z"></path><path d="M8.60156 15.2001H13.4016C13.879 15.2001 14.3368 15.0105 14.6744 14.6729C15.0119 14.3353 15.2016 13.8775 15.2016 13.4001V8.6001H8.60156V15.2001Z"></path></svg></span><span class="menu-bar__name">Dashboard</span></span></a></li>
								<li><a href="{{ route('wilayah.index') }}"><span class="menu-bar__text"><span class="nftmax-menu-icon nftmax-svg-icon__v2"><svg class="nftmax-svg-icon" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M8.40093 8.39892C8.07534 8.74593 7.8023 9.13874 7.59052 9.56484L13.3422 15.3165C13.4732 15.4475 13.651 15.5211 13.8362 15.5211C14.0215 15.5211 14.1992 15.4475 14.3303 15.3165L15.3185 14.3283C15.3834 14.2634 15.4349 14.1864 15.47 14.1016C15.5052 14.0168 15.5232 13.9259 15.5232 13.8342C15.5232 13.7424 15.5052 13.6515 15.47 13.5667C15.4349 13.482 15.3834 13.4049 15.3185 13.34L9.56685 7.5899C9.13999 7.79984 8.74699 8.07254 8.40093 8.39892Z"></path><path d="M4.94216 4.94003C4.41373 5.43043 3.80202 5.8226 3.1358 6.09809L6.74747 9.70976C7.02543 9.04469 7.41799 8.43359 7.90728 7.90427C8.43576 7.4139 9.04746 7.02168 9.71363 6.74604L6.10179 3.13507C5.82373 3.79989 5.43125 4.41079 4.94216 4.94003Z"></path><path d="M7.4116 0.494075L6.42351 1.48216C6.15066 1.75501 6.15066 2.19739 6.42351 2.47024L10.3758 6.42256C10.6487 6.69541 11.0911 6.69541 11.3639 6.42256L12.352 5.43448C12.6249 5.16163 12.6249 4.71925 12.352 4.4464L8.39968 0.494075C8.12683 0.221224 7.68445 0.221224 7.4116 0.494075Z"></path><path d="M1.48209 6.42236L0.49401 7.41044C0.221159 7.68329 0.221159 8.12567 0.49401 8.39852L4.44633 12.3508C4.71919 12.6237 5.16156 12.6237 5.43441 12.3508L6.4225 11.3628C6.69535 11.0899 6.69535 10.6475 6.4225 10.3747L2.47017 6.42236C2.19732 6.14951 1.75494 6.14951 1.48209 6.42236Z"></path></svg></span><span class="menu-bar__name">Wilayah</span><span class="menu-bar__count nft-gr-primary">{{ $SidebarInf['active_bids'] }}</span></span></a></li>
								<li><a href="{{ route('pelayanan.index') }}"><span class="menu-bar__text"><span class="nftmax-menu-icon nftmax-svg-icon__v3"><svg class="nftmax-svg-icon" viewBox="0 0 13 14" xmlns="http://www.w3.org/2000/svg"><path d="M10.2704 9.42604C9.70288 9.42584 9.14549 9.27516 8.65433 8.98917C8.16317 9.27516 7.60578 9.42584 7.03831 9.42604H5.96096C5.39271 9.42532 4.83491 9.27227 4.34494 8.98267C3.85499 9.27232 3.29717 9.42537 2.72891 9.42604H2.19024C1.62228 9.42585 1.06457 9.27377 0.574219 8.98538V11.5942C0.574219 12.0254 0.744478 12.439 1.04754 12.744C1.3506 13.0489 1.76165 13.2202 2.19024 13.2202H10.809C11.2376 13.2202 11.6487 13.0489 11.9517 12.744C12.2548 12.439 12.425 12.0254 12.425 11.5942V8.98592C11.9346 9.27391 11.3769 9.42578 10.809 9.42604H10.2704Z"></path><path d="M9.20833 0V3.24641H8.125V0H4.875V3.24641H3.79167V0H1.19167L0.013 5.29327L0 5.95175C0 6.52575 0.228273 7.07624 0.634602 7.48212C1.04093 7.888 1.59203 8.11602 2.16667 8.11602H2.70833C3.01703 8.11503 3.32189 8.04774 3.60227 7.91872C3.88264 7.78971 4.13197 7.60198 4.33333 7.36827C4.5347 7.60198 4.78402 7.78971 5.0644 7.91872C5.34477 8.04774 5.64964 8.11503 5.95833 8.11602H7.04167C7.34923 8.11618 7.65329 8.05077 7.93352 7.92415C8.21375 7.79753 8.4637 7.61263 8.66667 7.38179C8.86963 7.61263 9.11958 7.79753 9.39981 7.92415C9.68004 8.05077 9.9841 8.11618 10.2917 8.11602H10.8333C11.408 8.11602 11.9591 7.888 12.3654 7.48212C12.7717 7.07624 13 6.52575 13 5.95175V5.41068L11.8083 0H9.20833Z"></path></svg></span><span class="menu-bar__name">Pelayanan</span><span class="menu-bar__count ntfmax__bgc--2">{{ $SidebarInf['market_place'] }}</span></span></a></li>
								<li><a href="{{ route('pembimbing.index') }}"><span class="menu-bar__text"><span class="nftmax-menu-icon nftmax-svg-icon__v4"><svg class="nftmax-svg-icon" viewBox="0 0 16 13" xmlns="http://www.w3.org/2000/svg"><path d="M14.0764 6.57306C13.201 6.57306 12.4115 6.56813 11.6219 6.57503C10.998 6.57996 10.8122 6.76339 10.8031 7.37187C10.7981 7.74959 10.7971 8.12828 10.8062 8.506C10.8183 9.04051 11.0303 9.25353 11.5745 9.25945C12.3812 9.26734 13.1879 9.26142 14.0016 9.26142C14.0016 10.1164 14.0905 10.9547 13.9754 11.7663C13.8704 12.5099 13.1172 12.9902 12.2933 12.9922C8.78993 13.0011 5.28652 13.003 1.7831 12.9912C0.778516 12.9873 0.0192745 12.2476 0.0122071 11.2713C-0.00394697 9.0346 -0.00394697 6.79889 0.0122071 4.56221C0.0192745 3.57602 0.773468 2.84623 1.78108 2.84327C5.2845 2.83341 8.78791 2.8344 12.2913 2.84327C13.3131 2.84525 14.0481 3.55333 14.0743 4.54249C14.0905 5.19633 14.0764 5.85215 14.0764 6.57306Z"></path><path d="M12.8989 2.62038C12.7232 2.62038 12.5758 2.61052 12.4294 2.62235C12.0619 2.65095 11.9468 2.50697 11.9498 2.13714C11.9559 1.16772 11.5136 0.8403 10.5545 1.01486C7.72955 1.52866 4.90561 2.04247 2.07764 2.53852C1.30931 2.67265 0.558145 2.80578 0.0351562 3.54839C0.0927052 2.58093 0.718676 1.86693 1.70912 1.68152C4.7784 1.10854 7.85171 0.557262 10.9281 0.0227454C11.6085 -0.0955978 12.1295 0.258446 12.5162 0.783101C12.9221 1.3334 12.9746 1.95963 12.8989 2.62038Z"></path><path d="M13.0971 9.03944C12.5933 9.03944 12.0895 9.03747 11.5857 9.04043C11.2142 9.0424 11.0234 8.87475 11.0274 8.50394C11.0314 8.11045 11.0304 7.71696 11.0284 7.32347C11.0264 6.9566 11.2061 6.78895 11.5867 6.79191C12.5944 6.7998 13.602 6.80177 14.6106 6.79289C14.9922 6.78994 15.1699 6.95364 15.1669 7.32248C15.1628 7.71597 15.1618 8.10946 15.1659 8.50295C15.1699 8.87179 14.9872 9.0424 14.6106 9.03747C14.1068 9.03155 13.603 9.0355 13.0992 9.0355C13.0971 9.03648 13.0971 9.03846 13.0971 9.03944ZM13.8645 7.42406C13.6848 7.64299 13.4788 7.79191 13.502 7.88856C13.5373 8.03451 13.7322 8.14398 13.8594 8.26824C13.9755 8.14891 14.1674 8.03944 14.1845 7.90828C14.1997 7.79092 14.023 7.6499 13.8645 7.42406Z"></path></svg></span><span class="menu-bar__name">Pembimbing</span></span></a></li>
								<li><a href="{{ route('anak_bimbingan.index') }}"><span class="menu-bar__text"><span class="nftmax-menu-icon nftmax-svg-icon__v5"><svg class="nftmax-svg-icon" viewBox="0 0 15 15" xmlns="http://www.w3.org/2000/svg"><path d="M14.1755 7.88376L11.9493 9.56253L12.7948 12.266C12.9314 12.6853 12.9331 13.1389 12.7997 13.5593C12.6663 13.9797 12.4049 14.3444 12.0544 14.5988C11.7099 14.8615 11.2925 15.0022 10.8643 15C10.436 14.9978 10.02 14.8528 9.67807 14.5866L7.50125 12.9323L5.3238 14.5846C4.97996 14.8458 4.56476 14.9876 4.13792 14.9898C3.71108 14.9919 3.29457 14.8542 2.94827 14.5966C2.60197 14.3389 2.34373 13.9745 2.21066 13.5557C2.0776 13.1369 2.07657 12.6854 2.20772 12.266L3.05318 9.56253L0.826957 7.88376C0.483556 7.62452 0.22828 7.25987 0.0975914 6.84188C-0.0330973 6.4239 -0.0325136 5.97396 0.0992584 5.55633C0.23103 5.13871 0.487251 4.77476 0.831323 4.51648C1.17539 4.25819 1.58972 4.11878 2.01511 4.11815H4.74974L5.57957 1.44762C5.71006 1.02726 5.96649 0.660548 6.31187 0.400372C6.65724 0.140196 7.07372 0 7.50125 0C7.92878 0 8.34526 0.140196 8.69064 0.400372C9.03601 0.660548 9.29244 1.02726 9.42293 1.44762L10.2528 4.11815H12.9849C13.4103 4.11878 13.8246 4.25819 14.1687 4.51648C14.5127 4.77476 14.769 5.13871 14.9007 5.55633C15.0325 5.97396 15.0331 6.4239 14.9024 6.84188C14.7717 7.25987 14.5164 7.62452 14.173 7.88376H14.1755Z"></path></svg></span><span class="menu-bar__name">Anak Bimbingan</span></span></a></li>
							</ul>
						</div>
						<!-- End Nav Menu -->
					</div>
					
					<div class="admin-menu__two mg-top-50">
						<h4 class="admin-menu__title nftmax-scolor">Settings</h4>
						<!-- Nav Menu -->
						<div class="menu-bar">
							<ul class="menu-bar__one">
								<li><a href="{{url('/my-profile')}}"><span class="menu-bar__text"><span class="nftmax-menu-icon nftmax-svg-icon__v10"><svg class="nftmax-svg-icon"  viewBox="0 0 15 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.8692 11.6667H4.13085C3.03569 11.668 1.98576 12.1036 1.21136 12.878C0.436961 13.6524 0.00132319 14.7023 0 15.7975V20H15.0001V15.7975C14.9987 14.7023 14.5631 13.6524 13.7887 12.878C13.0143 12.1036 11.9644 11.668 10.8692 11.6667Z"></path><path d="M7.49953 10C10.261 10 12.4995 7.76145 12.4995 5.00002C12.4995 2.23858 10.261 0 7.49953 0C4.7381 0 2.49951 2.23858 2.49951 5.00002C2.49951 7.76145 4.7381 10 7.49953 10Z"></path></svg></span><span class="menu-bar__name">My Profile</span> </span></a></li>
								<li><a href="{{ url('/setting') }}"><span class="menu-bar__text"><span class="nftmax-menu-icon nftmax-svg-icon__v11"><svg class="nftmax-svg-icon" viewBox="0 0 14 16" xmlns="http://www.w3.org/2000/svg"><path d="M0.259306 11.548C0.789972 12.469 1.96681 12.7854 2.88783 12.2548C2.88846 12.2544 2.88906 12.254 2.88969 12.2537L3.17517 12.0888C3.71408 12.5499 4.33352 12.9074 5.00231 13.1435V13.4727C5.00231 14.5356 5.86402 15.3973 6.92698 15.3973C7.98993 15.3973 8.85164 14.5356 8.85164 13.4727V13.1435C9.52052 12.9071 10.14 12.5491 10.6788 12.0875L10.9656 12.253C11.8868 12.7845 13.0645 12.4686 13.5959 11.5473C14.1274 10.6261 13.8115 9.44842 12.8902 8.91694L12.6054 8.75271C12.7336 8.05499 12.7336 7.33974 12.6054 6.64199L12.8902 6.47776C13.8114 5.94628 14.1274 4.76863 13.5959 3.84738C13.0645 2.92615 11.8868 2.61018 10.9656 3.14165L10.6801 3.30654C10.1406 2.84604 9.52076 2.48913 8.85164 2.25378V1.92467C8.85164 0.861709 7.98993 0 6.92698 0C5.86402 0 5.00231 0.861709 5.00231 1.92467V2.25378C4.33343 2.49025 3.71396 2.84823 3.17517 3.30979L2.8884 3.14364C1.96715 2.61213 0.789491 2.92811 0.258013 3.84933C-0.273465 4.77055 0.0424806 5.94824 0.963734 6.47972L1.24858 6.64394C1.12035 7.34166 1.12035 8.05692 1.24858 8.75467L0.963734 8.9189C0.0450368 9.45179 -0.269887 10.6272 0.259306 11.548ZM6.92698 5.13245C8.34425 5.13245 9.49319 6.28139 9.49319 7.69866C9.49319 9.11594 8.34425 10.2649 6.92698 10.2649C5.5097 10.2649 4.36077 9.11594 4.36077 7.69866C4.36077 6.28139 5.5097 5.13245 6.92698 5.13245Z"></path></svg></span><span class="menu-bar__name">Settings</span></span></a></li>
							</ul>
						</div>
						<!-- End Nav Menu -->
					</div>
					
					<!-- Logout Button -->
					<div class="logout-button">
						<a class="nftmax-btn primary" data-bs-toggle="modal" data-bs-target="#logout_modal"><div class="logo-button__icon"><img src="assets/img/logout.png" alt="#"></div><span class="menu-bar__name">Signout</span></a>
					</div>
				</div>
				<!-- End Admin Menu -->
			</div>
			<!-- End NFTMax Admin Menu -->
			
			<!-- Logout Modal  -->
			<div class="nftmax-preview__modal modal fade" id="logout_modal" tabindex="-1" aria-labelledby="logoutmodal" aria-hidden="true" >
				<div class="modal-dialog modal-dialog-centered nftmax-close__modal-close">
					<div class="modal-content nftmax-preview__modal-content">
						<div class="modal-header nftmax__modal__header">
							<h4 class="modal-title nftmax-preview__modal-title" id="logoutmodal">Confirm</h4>
							<button type="button" class="nftmax-preview__modal--close btn-close" data-bs-dismiss="modal" aria-label="Close"><svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M36 16.16C36 17.4399 36 18.7199 36 20.0001C35.7911 20.0709 35.8636 20.2554 35.8385 20.4001C34.5321 27.9453 30.246 32.9248 22.9603 35.2822C21.9006 35.6251 20.7753 35.7657 19.6802 35.9997C18.4003 35.9997 17.1204 35.9997 15.8401 35.9997C15.5896 35.7086 15.2189 35.7732 14.9034 35.7093C7.77231 34.2621 3.08728 30.0725 0.769671 23.187C0.435002 22.1926 0.445997 21.1199 0 20.1599C0 18.7198 0 17.2798 0 15.8398C0.291376 15.6195 0.214408 15.2656 0.270759 14.9808C1.71321 7.69774 6.02611 2.99691 13.0428 0.700951C14.0118 0.383805 15.0509 0.386897 15.9999 0C17.2265 0 18.4532 0 19.6799 0C19.7156 0.124041 19.8125 0.136067 19.9225 0.146719C27.3 0.868973 33.5322 6.21922 35.3801 13.427C35.6121 14.3313 35.7945 15.2484 36 16.16ZM33.011 18.0787C33.0433 9.77105 26.3423 3.00309 18.077 2.9945C9.78479 2.98626 3.00344 9.658 2.98523 17.8426C2.96667 26.1633 9.58859 32.9601 17.7602 33.0079C26.197 33.0577 32.9787 26.4186 33.011 18.0787Z" fill="#374557" fill-opacity="0.6"></path><path d="M15.9309 18.023C13.9329 16.037 12.007 14.1207 10.0787 12.2072C9.60071 11.733 9.26398 11.2162 9.51996 10.506C9.945 9.32677 11.1954 9.0811 12.1437 10.0174C13.9067 11.7585 15.6766 13.494 17.385 15.2879C17.9108 15.8401 18.1633 15.7487 18.6375 15.258C20.3586 13.4761 22.1199 11.7327 23.8822 9.99096C24.8175 9.06632 26.1095 9.33639 26.4967 10.517C26.7286 11.2241 26.3919 11.7413 25.9133 12.2178C24.1757 13.9472 22.4477 15.6855 20.7104 17.4148C20.5228 17.6018 20.2964 17.7495 20.0466 17.9485C22.0831 19.974 24.0372 21.8992 25.9689 23.8468C26.9262 24.8119 26.6489 26.1101 25.4336 26.4987C24.712 26.7292 24.2131 26.3441 23.7455 25.8757C21.9945 24.1227 20.2232 22.3892 18.5045 20.6049C18.0698 20.1534 17.8716 20.2269 17.4802 20.6282C15.732 22.4215 13.9493 24.1807 12.1777 25.951C11.7022 26.4262 11.193 26.7471 10.4738 26.4537C9.31345 25.9798 9.06881 24.8398 9.98589 23.8952C11.285 22.5576 12.6138 21.2484 13.9387 19.9355C14.5792 19.3005 15.2399 18.6852 15.9309 18.023Z" fill="#374557" fill-opacity="0.6"></path></svg></button>
						</div>
						<div class="modal-body nftmax-modal__body modal-body nftmax-close__body">
							<div class="nftmax-preview__close">
								<div class="nftmax-preview__close-img"><img src="assets/img/close.png" alt="#"></div>
								<h2 class="nftmax-preview__close-title">Are you sure you want to Logout <span>NETMAX admin Penal?</span></h2>
								<div class="nftmax__item-button--group">
									<a href="{{ url('/load-login') }}" class="nftmax__item-button--single nftmax-btn nftmax-btn__bordered bg radius " type="submit">Yes Logout 
									</a>																
									<button class="nftmax__item-button--single nftmax-btn nftmax-btn__bordered--plus radius" data-bs-dismiss="modal"><span class="ntfmax__btn-textgr">Not Now</span> </button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End Logout Modal -->
			
			<!-- Connect to Wallet -->
			<div class="nftmax-preview__modal modal fade" id="add_wallet" tabindex="-1" aria-labelledby="addWalletlLabel" aria-hidden="true" >
				<div class="modal-dialog modal-dialog-centered nftmax-followers__modal">
					<div class="modal-content nftmax-preview__connect">
						<div class="modal-header nftmax__modal__header">
							<h4 class="modal-title nftmax-preview__modal-title" id="addWalletlLabel">Connect to a wallet</h4>
							<button type="button" class="nftmax-preview__modal--close btn-close" data-bs-dismiss="modal" aria-label="Close"><svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M36 16.16C36 17.4399 36 18.7199 36 20.0001C35.7911 20.0709 35.8636 20.2554 35.8385 20.4001C34.5321 27.9453 30.246 32.9248 22.9603 35.2822C21.9006 35.6251 20.7753 35.7657 19.6802 35.9997C18.4003 35.9997 17.1204 35.9997 15.8401 35.9997C15.5896 35.7086 15.2189 35.7732 14.9034 35.7093C7.77231 34.2621 3.08728 30.0725 0.769671 23.187C0.435002 22.1926 0.445997 21.1199 0 20.1599C0 18.7198 0 17.2798 0 15.8398C0.291376 15.6195 0.214408 15.2656 0.270759 14.9808C1.71321 7.69774 6.02611 2.99691 13.0428 0.700951C14.0118 0.383805 15.0509 0.386897 15.9999 0C17.2265 0 18.4532 0 19.6799 0C19.7156 0.124041 19.8125 0.136067 19.9225 0.146719C27.3 0.868973 33.5322 6.21922 35.3801 13.427C35.6121 14.3313 35.7945 15.2484 36 16.16ZM33.011 18.0787C33.0433 9.77105 26.3423 3.00309 18.077 2.9945C9.78479 2.98626 3.00344 9.658 2.98523 17.8426C2.96667 26.1633 9.58859 32.9601 17.7602 33.0079C26.197 33.0577 32.9787 26.4186 33.011 18.0787Z" fill="#374557" fill-opacity="0.6"></path><path d="M15.9309 18.023C13.9329 16.037 12.007 14.1207 10.0787 12.2072C9.60071 11.733 9.26398 11.2162 9.51996 10.506C9.945 9.32677 11.1954 9.0811 12.1437 10.0174C13.9067 11.7585 15.6766 13.494 17.385 15.2879C17.9108 15.8401 18.1633 15.7487 18.6375 15.258C20.3586 13.4761 22.1199 11.7327 23.8822 9.99096C24.8175 9.06632 26.1095 9.33639 26.4967 10.517C26.7286 11.2241 26.3919 11.7413 25.9133 12.2178C24.1757 13.9472 22.4477 15.6855 20.7104 17.4148C20.5228 17.6018 20.2964 17.7495 20.0466 17.9485C22.0831 19.974 24.0372 21.8992 25.9689 23.8468C26.9262 24.8119 26.6489 26.1101 25.4336 26.4987C24.712 26.7292 24.2131 26.3441 23.7455 25.8757C21.9945 24.1227 20.2232 22.3892 18.5045 20.6049C18.0698 20.1534 17.8716 20.2269 17.4802 20.6282C15.732 22.4215 13.9493 24.1807 12.1777 25.951C11.7022 26.4262 11.193 26.7471 10.4738 26.4537C9.31345 25.9798 9.06881 24.8398 9.98589 23.8952C11.285 22.5576 12.6138 21.2484 13.9387 19.9355C14.5792 19.3005 15.2399 18.6852 15.9309 18.023Z" fill="#374557" fill-opacity="0.6"></path></svg></button>
						</div>
						<div class="modal-body nftmax-modal__body">
							<!-- Treadning Action Single -->
							<ul class="nftmax-wallet__list">
								<!-- Single Wallet -->
								@foreach($Addmoney as $Addmoney)
								<li  class="nftmax-wallet__list-single">
									<div class="nftmax-balance-info">
										<div class="nftmax-balance__img"><img src="{{asset($Addmoney['0'])}}" alt="#"></div>
										<h4 class="nftmax-balance-name">{{ $Addmoney['1'] }}</h4>
									</div>
									@if($Addmoney['2'] == 1)
									<a class="nftmax-btn nftmax-btn__bordered--plus radius" tabindex="-1" role="button" aria-disabled="true"><span class="ntfmax__btn-textgr">Connected</span></a>
									@else
									<a class="nftmax-btn nftmax-btn__bordered bg radius" tabindex="-1" role="button" aria-disabled="true">Connect</a>
									@endif
								</li>
								@endforeach
							</ul>
							<div class="view-wallet__btn"><a href="{{url('/my-wallet')}}">View All Wallet</a></div>
						</div>
					</div>
				</div>
			</div>
			<!-- End Connect to Wallet -->
			
			<!-- Start Header -->
			<header class="nftmax-header">
				<div class="container">
					<div class="row g-50">
						<div class="col-12">
							<!-- Dashboard Header -->
							<div class="nftmax-header__inner">
								<div class="nftmax__sicon close-icon d-xl-none"><img src="assets/img/menu-toggle.svg" alt="#"></div>
								<div class="nftmax-header__left">
									<!-- Search Form -->
									<div class="nftmax-header__form">
										<form class="nftmax-header__form-inner" action="#">
											<button class="search-btn" type="submit"><img src="assets/img/search.png" alt="#"></button>
											<input name="s" value="" type="text" placeholder="Search items, collections...">
										</form>
									</div>
									<!-- End Search Form -->
								</div>
								<div class="nftmax-header__right">
									<div class="nftmax-header__group">
										<!-- Search Form -->
										<div class="nftmax-header__amount">
											<div class="nftmax-amount__icon"><img src="assets/img/bag-icon.svg" alt="#"></div>
											<div class="nftmax-amount__digit"><span>$</span> 234,435.34</div>
											<div class="nftmax-header__plus"><a href="#"><img src="assets/img/plus-icon.svg" alt="#"></a></div>
											<!-- NFTMax Balance Hover -->
											<div class="nftmax-balance">
												<h3 class="nftmax-balance__title">Your Balance</h3>
												<!-- NFTMax Balance List -->
												<ul class="nftmax-balance_list">
													@foreach($Balance as $Balance)
													<li>
														<div class="nftmax-balance-info">
															<div class="nftmax-balance__img"><img src="{{ asset($Balance['0']) }}" alt="#"></div>
															<h4 class="nftmax-balance-name">{{ $Balance['1'] }}</h4>
														</div>
														<div class="nftmax-balance-amount">
															<h4 class="nftmax-balance-amount nftmax-scolor">{{ $Balance['2'] }} ETH <span  class="nftmax-balance-usd">({{ $Balance['3'] }}  USD)</span></h4>
														</div>
													</li>
													@endforeach
												</ul>
												<!-- NFTMax Balance Button -->
												<div class="nftmax-balance__button" data-bs-toggle="modal" data-bs-target="#add_wallet"><a href="#" class="nftmax-btn nftmax-btn__bordered bg radius" >Add Money</a></div>
												
											</div>
											<!-- End NFTMax Balance Hover -->
										</div>
										<!-- End Search Form -->
										<div class="nftmax-header__group-two">
											<div class="nftmax-header__right">
												<div class="nftmax-header__alarm"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="24" viewBox="0 0 22 24" fill="none"><path d="M3.27336 17.6123H18.3622C18.8266 17.6123 19.2841 17.5005 19.6961 17.2863C20.1081 17.072 20.4625 16.7617 20.7292 16.3815C20.9959 16.0014 21.1671 15.5626 21.2283 15.1023C21.2896 14.6419 21.239 14.1736 21.081 13.737L18.3323 6.13763C17.8629 4.44882 16.8532 2.96026 15.4577 1.89961C14.0621 0.838957 12.3576 0.264609 10.6048 0.264404V0.264404C8.78009 0.264376 7.00995 0.886604 5.58662 2.02836C4.16328 3.17011 3.17186 4.76311 2.77605 6.54435L0.511187 13.869C0.377839 14.3015 0.347861 14.7592 0.423665 15.2053C0.49947 15.6515 0.678939 16.0736 0.947633 16.4377C1.21633 16.8018 1.56674 17.0978 1.97069 17.3018C2.37463 17.5058 2.82082 17.6122 3.27336 17.6123Z" fill="#374557"></path><path d="M6.19531 19.5398C6.41651 20.6291 7.00751 21.6085 7.86817 22.312C8.72883 23.0154 9.80622 23.3997 10.9178 23.3997C12.0294 23.3997 13.1068 23.0154 13.9674 22.312C14.8281 21.6085 15.4191 20.6291 15.6403 19.5398H6.19531Z" fill="#374557"></path></svg><span class="nftmax-header__count">{{ count($RecentNotification) }}</span>
													<!-- NFTMax Alarm Hover -->
													<div class="nftmax-balance nftmax-alarm__hover">
														<h3 class="nftmax-balance__title">Recent Notification</h3>
														<!-- NFTMax Balance List -->
														<ul class="nftmax-balance_list">
															@foreach($RecentNotification as $RecentNotification)
															<li>
																<div class="nftmax-balance-info">
																	<div class="nftmax-balance__img nftmax-alarm__default"><img src="{{ asset($RecentNotification['0']) }}" alt="#"></div>
																	<div class="nftmax-alarm__content">
																		<h4 class="nftmax-balance-name">{{$RecentNotification['1']}} <strong class="nftmax-balance__second">{{$RecentNotification['2']}}</strong></h4>
																		<p class="nftmax-alarm__text">{{$RecentNotification['3']}}</p>
																	</div>
																</div>
															</li>
															@endforeach
														</ul>
														<!-- NFTMax Balance Button -->
														<div class="nftmax-balance__button"><a href="{{ url('/notification') }}" class="nftmax-balance__sell-all">See all Notification</a></div>
													</div>
													<!-- End NFTMax Balance Hover -->
												</div>
												<div class="nftmax-header__author">
													<div class="nftmax-header__author-img"><img src="{{ asset($User_info_for_header['image']) }}" alt="#"></div>
													<div class="nftmax-header__author-content">
														<h4 class="nftmax-header__author-title">{{ $User_info_for_header['name'] }}</h4>
														<p class="nftmax-header__author-text v1"><a href="#">{{ $User_info_for_header['user_name'] }}</a></p>
													</div>
													
													<!-- NFTMax Profile Hover -->
													<div class="nftmax-balance nftmax-profile__hover">
														<h3 class="nftmax-balance__title">My Profile</h3>
														<!-- NFTMax Balance List -->
														<ul class="nftmax-balance_list">
															<li>
																<div class="nftmax-balance-info">
																	<div class="nftmax-balance__img nftmax-profile__img-one"><img src="assets/img/profile-1.png" alt="#"></div>
																	<h4 class="nftmax-balance-name"><a href="{{url('/my-profile')}}">My Profile</a></h4>
																</div>
															</li>
															<li>
																<div class="nftmax-balance-info">
																	<div class="nftmax-balance__img nftmax-profile__img-two"><img src="assets/img/profile-2.png" alt="#"></div>
																	<h4 class="nftmax-balance-name"><a href="{{url('/message')}}">Message</a><span class="nftmax-profile__count">19</span></h4>
																</div>
															</li>
															<li>
																<div class="nftmax-balance-info">
																	<div class="nftmax-balance__img nftmax-profile__img-three"><img src="assets/img/profile-3.png" alt="#"></div>
																	<h4 class="nftmax-balance-name"><a href="{{url('/history')}}">History</a></h4>
																</div>
															</li>
															<li>
																<div class="nftmax-balance-info">
																	<div class="nftmax-balance__img nftmax-profile__img-four"><img src="assets/img/profile-4.png" alt="#"></div>
																	<h4 class="nftmax-balance-name"><a href="{{url('/setting')}}">Setting</a></h4>
																</div>
															</li>
															<li>
																<div class="nftmax-balance-info">
																	<div class="nftmax-balance__img nftmax-profile__img-five"><img src="assets/img/profile-5.png" alt="#"></div>
																	<h4 class="nftmax-balance-name"><a href="{{url('/load-login')}}">Log Out</a></h4>
																</div>
															</li>
														</ul>
													</div>
													<!-- End NFTMax Balance Hover -->
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</header>
			<!-- End Header -->
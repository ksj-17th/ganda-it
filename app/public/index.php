<?php include '_header.php'; ?>

<div class="card">
    <span class="eyebrow">Secure File Transfer System</span>

    <h1>Ganda-it</h1>

    <p class="lead">
        <span id="typing-text"></span><br>
        국내 최고 기업간 신뢰성 있는 파일 전송 서비스를 제공합니다.
    </p>


    <h3>Function</h3>

    <ul class="feature-list">
         <li>🔐 <strong>사용자 로그인</strong> 안전한 사용자 인증</li>
        <li>📁 <strong>파일 목록 / 다운로드</strong> 업로드된 파일을 간편하게 관리</li>
        <li>🔗 <strong>외부 공유 링크</strong> 안전하고 간편한 파일 공유</li>
        <li>📥 <strong>관리자 파일 Import</strong> 관리자가 파일을 손쉽게 관리</li>
    </ul>
</div>

<div class="running-tiger">
    <img src="/images/movingTiger.gif" alt="">
</div>

<style>
    
.running-tiger {
    position: fixed;
    bottom: 20px;
    left: -220px;
    width: 160px;
    z-index: 99999;
    pointer-events: none;
    animation: run-tiger 7s linear infinite;
}

.running-tiger img {
    width: 100%;
    display: block;
}

@keyframes run-tiger {
    0% {
        left: -220px;
    }

    100% {
        left: 100%;
    }
}

#typing-text {
    font-family: "Jalnan", sans-serif;
    font-size: 24px;
    font-weight: 700;
    color: #2563eb;
}
</style>

<script>
const text = "간다간다 파일 간다-잇!";
const target = document.getElementById("typing-text");

let index = 0;

function typing() {
    if (index < text.length) {
        target.textContent += text.charAt(index);
        index++;
        setTimeout(typing, 100);
    }
}

typing();
</script>

<?php include '_footer.php'; ?>

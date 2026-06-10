@extends('layouts.main')
@section('content')
@php use App\Helpers\BookVerseHelper; @endphp

<style>
    :root {
        --community-theme: {{ $community->tema_warna ?? 'var(--primary)' }};
        --chat-bg: var(--bg);
        --chat-mine: var(--primary-light);
        --chat-other: var(--bg-white);
    }
    footer { display: none !important; }

    .chat-container {
        max-width: 900px;
        margin: 0 auto;
        height: 80vh;
        display: flex;
        flex-direction: column;
        border-radius: var(--radius-xl);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border);
        background: var(--chat-bg);
        position: relative;
    }

    .chat-header {
        background: var(--bg-card);
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        border-bottom: 1px solid var(--border);
        z-index: 10;
    }

    .chat-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .message-bubble {
        max-width: 75%;
        padding: 8px 12px;
        border-radius: var(--radius-md);
        position: relative;
        font-size: 0.95rem;
        box-shadow: var(--shadow-xs);
        word-break: break-word;
    }

    .message-mine {
        align-self: flex-end;
        background: var(--chat-mine);
        border-top-right-radius: 0;
        color: var(--primary-dark);
    }

    .message-other {
        align-self: flex-start;
        background: var(--chat-other);
        border: 1px solid var(--border);
        border-top-left-radius: 0;
    }

    .message-sender {
        font-size: 0.8rem;
        font-weight: bold;
        color: var(--community-theme);
        margin-bottom: 3px;
    }

    .message-time {
        font-size: 0.7rem;
        color: var(--text-muted);
        text-align: right;
        margin-top: 5px;
    }

    .message-reply-preview {
        background: rgba(0,0,0,0.05);
        border-left: 4px solid var(--community-theme);
        padding: 5px 10px;
        border-radius: 4px;
        margin-bottom: 5px;
        font-size: 0.85rem;
    }
    html.dark .message-reply-preview { background: rgba(255,255,255,0.05); }

    .chat-media-grid {
        display: grid;
        gap: 5px;
        margin-bottom: 5px;
        border-radius: 8px;
        overflow: hidden;
    }
    .chat-media-grid.count-1 {
        grid-template-columns: 1fr;
        max-width: 250px;
    }
    .media-item-container {
        position: relative;
        cursor: pointer;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }
    .media-item-container img, .media-item-container video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        max-height: 300px;
        background: var(--bg-body);
    }
    .media-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.8rem;
        font-weight: bold;
        pointer-events: none;
    }

    /* Lightbox */
    .lightbox-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0; top: 0; right: 0; bottom: 0;
        background-color: rgba(0,0,0,0.9);
        backdrop-filter: blur(5px);
    }
    .lightbox-content {
        position: relative;
        margin: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        width: 100%;
    }
    .lightbox-media {
        max-width: 90%;
        max-height: 90vh;
        object-fit: contain;
    }
    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: white;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        z-index: 10001;
        transition: 0.2s;
    }
    .lightbox-close:hover { color: #ddd; }
    .lightbox-prev, .lightbox-next {
        cursor: pointer;
        position: absolute;
        top: 50%;
        width: auto;
        padding: 20px;
        margin-top: -50px;
        color: white;
        font-weight: bold;
        font-size: 40px;
        user-select: none;
        z-index: 10001;
        transition: 0.2s;
        background: rgba(0,0,0,0.3);
        border-radius: 8px;
    }
    .lightbox-prev:hover, .lightbox-next:hover { background: rgba(0,0,0,0.8); }
    .lightbox-prev { left: 20px; }
    .lightbox-next { right: 20px; }

    .chat-input-area {
        background: var(--bg-card);
        padding: 15px 20px;
        border-top: 1px solid var(--border);
        position: relative;
    }

    .replying-to-box {
        display: none;
        background: var(--bg-body);
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 10px;
        border-left: 4px solid var(--community-theme);
        position: relative;
    }

    .media-preview-box {
        display: none;
        background: var(--bg-body);
        padding: 10px 40px 10px 10px;
        border-radius: 8px;
        margin-bottom: 10px;
        position: relative;
        overflow-x: auto;
        white-space: nowrap;
    }
    .media-preview-item {
        display: inline-block;
        position: relative;
        margin-right: 10px;
        vertical-align: top;
    }
    .media-preview-item img, .media-preview-item video {
        max-height: 100px;
        max-width: 150px;
        border-radius: 8px;
        object-fit: cover;
    }

    .cancel-btn {
        position: absolute;
        right: 10px;
        top: 10px;
        cursor: pointer;
        color: var(--text-muted);
        background: var(--bg-card);
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        z-index: 5;
    }

    .chat-input-row {
        display: flex;
        gap: 10px;
        align-items: flex-end;
    }

    .action-btn {
        background: transparent;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--text-muted);
        padding: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .action-btn:hover { color: var(--community-theme); }

    .chat-input {
        flex: 1;
        border: none;
        background: var(--bg-body);
        border-radius: 20px;
        padding: 12px 20px;
        resize: none;
        font-size: 1rem;
        outline: none;
        max-height: 100px;
        color: var(--text-primary);
    }

    .btn-send {
        background: var(--community-theme);
        color: white;
        border: none;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .btn-send:hover { opacity: 0.9; }

    .chat-messages::-webkit-scrollbar { width: 6px; }
    .chat-messages::-webkit-scrollbar-track { background: transparent; }
    .chat-messages::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.2); border-radius: 10px; }
    html.dark .chat-messages::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); }

    .reply-btn {
        opacity: 0;
        visibility: hidden;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        background: var(--bg-card);
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        border: none;
        font-size: 0.9rem;
        transition: 0.2s;
    }
    .reply-btn::before {
        content: '';
        position: absolute;
        top: -15px; left: -15px; right: -15px; bottom: -15px;
    }
    .message-bubble:hover .reply-btn { opacity: 1; visibility: visible; }
    .message-mine .reply-btn { left: -45px; }
    .message-other .reply-btn { right: -45px; }

    .chat-status {
        text-align: center;
        padding: 10px;
        color: var(--text-muted);
        font-size: 0.85rem;
    }

    #emoji-picker {
        display: none;
        position: absolute;
        bottom: 80px;
        left: 20px;
        z-index: 100;
    }

    .reaction-picker {
        position: absolute;
        bottom: 100%;
        margin-bottom: 5px;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-full);
        padding: 5px 10px;
        display: flex;
        gap: 8px;
        box-shadow: var(--shadow-md);
        opacity: 0;
        visibility: hidden;
        transition: 0.2s;
        z-index: 20;
    }
    .message-mine .reaction-picker { right: 0; }
    .message-other .reaction-picker { left: 0; }
    .message-bubble:hover .reaction-picker {
        opacity: 1;
        visibility: visible;
    }
    .reaction-picker::after {
        content: '';
        position: absolute;
        top: -10px; left: 0; right: 0; bottom: -15px;
        z-index: -1;
    }
    .reaction-emoji {
        cursor: pointer;
        font-size: 1.2rem;
        transition: transform 0.2s;
        user-select: none;
    }
    .reaction-emoji:hover { transform: scale(1.3); }

    .reactions-container {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-top: 6px;
    }
    .reaction-badge {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-full);
        padding: 2px 6px;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 4px;
        box-shadow: var(--shadow-xs);
        cursor: pointer;
        user-select: none;
        transition: 0.2s;
    }
    .reaction-badge:hover { background: var(--bg); }
    .reaction-badge.user-reacted {
        background: var(--primary-light);
        border-color: var(--primary);
        color: var(--primary-dark);
    }
</style>

<div class="chat-container">
    {{-- Header --}}
    <div class="chat-header">
        <a href="{{ route('community.feed', $community->id) }}" style="text-decoration: none; font-size: 1.5rem; color: var(--text-primary);">❮</a>
        @if($community->logo_komunitas)
            <img src="{{ asset('storage/' . $community->logo_komunitas) }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
        @else
            <div style="width: 40px; height: 40px; border-radius: 50%; background: #ddd; display:flex; align-items:center; justify-content:center; font-size: 1.2rem;">🏘️</div>
        @endif
        <div style="flex: 1;">
            <h2 style="margin: 0; font-size: 1.1rem; line-height: 1;">{{ $community->nama_komunitas }}</h2>
            <span style="font-size: 0.8rem; color: var(--text-muted);">Live Chat</span>
        </div>
    </div>

    {{-- Messages --}}
    <div class="chat-messages" id="chatMessages">
        <div class="chat-status" id="chatStatus">Memuat pesan...</div>
    </div>

    {{-- Emoji Picker Container --}}
    <emoji-picker id="emoji-picker"></emoji-picker>

    {{-- Input Area --}}
    <div class="chat-input-area">
        {{-- Preview Areas --}}
        <div class="replying-to-box" id="replyBox">
            <span class="cancel-btn" id="cancelReplyBtn">✕</span>
            <div style="font-weight: bold; color: var(--community-theme); font-size: 0.85rem;" id="replyUsername"></div>
            <div style="font-size: 0.85rem; color: var(--text-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 90%;" id="replyText"></div>
        </div>
        <div class="media-preview-box" id="mediaBox">
            <span class="cancel-btn" id="cancelMediaBtn">✕</span>
            <!-- Items appended by JS -->
        </div>

        {{-- Input Row --}}
        <div class="chat-input-row">
            <input type="hidden" id="replyId" value="">
            <input type="file" id="mediaInput" accept="image/*,video/mp4,video/quicktime" style="display: none;" multiple>
            
            <button type="button" class="action-btn" id="emojiBtn">😀</button>
            <button type="button" class="action-btn" id="attachBtn">📎</button>
            
            <textarea id="chatInput" class="chat-input" placeholder="Ketik pesan..." rows="1"></textarea>
            <button type="button" class="btn-send" id="sendBtn">➤</button>
        </div>
    </div>
</div>

<!-- Lightbox Modal -->
<div id="lightboxModal" class="lightbox-modal">
    <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
    <span class="lightbox-prev" onclick="changeLightbox(-1)">&#10094;</span>
    <span class="lightbox-next" onclick="changeLightbox(1)">&#10095;</span>
    <div class="lightbox-content" id="lightboxContent"></div>
</div>

<script type="module">
import 'https://cdn.jsdelivr.net/npm/emoji-picker-element@1/index.js';

(function() {
    // === VARIABLES ===
    var lastChatId = 0;
    var komunitasId = {{ $community->id }};
    var csrfToken = '{{ csrf_token() }}';
    var isFetching = false;
    var isSending = false;
    window.allChats = []; // Store chats for lightbox

    var messagesEl = document.getElementById('chatMessages');
    var statusEl = document.getElementById('chatStatus');
    var inputEl = document.getElementById('chatInput');
    var sendBtn = document.getElementById('sendBtn');
    
    var replyBox = document.getElementById('replyBox');
    var replyIdEl = document.getElementById('replyId');
    var replyUsernameEl = document.getElementById('replyUsername');
    var replyTextEl = document.getElementById('replyText');
    var cancelReplyBtn = document.getElementById('cancelReplyBtn');

    var attachBtn = document.getElementById('attachBtn');
    var mediaInput = document.getElementById('mediaInput');
    var mediaBox = document.getElementById('mediaBox');
    var emojiBtn = document.getElementById('emojiBtn');
    var emojiPicker = document.getElementById('emoji-picker');

    // === EMOJI PICKER ===
    emojiBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        emojiPicker.style.display = emojiPicker.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', function(e) {
        if (e.target !== emojiPicker && e.target !== emojiBtn) {
            emojiPicker.style.display = 'none';
        }
    });

    emojiPicker.addEventListener('emoji-click', function(event) {
        var start = inputEl.selectionStart;
        var end = inputEl.selectionEnd;
        var text = inputEl.value;
        inputEl.value = text.substring(0, start) + event.detail.unicode + text.substring(end);
        inputEl.focus();
    });

    // === MEDIA PREVIEW ===
    attachBtn.addEventListener('click', function() {
        mediaInput.click();
    });

    mediaInput.addEventListener('change', function() {
        var files = this.files;
        if (!files || files.length === 0) return;

        if (files.length > 10) {
            alert('Maksimal hanya bisa mengunggah 10 file sekaligus.');
            this.value = '';
            return;
        }

        mediaBox.innerHTML = '<span class="cancel-btn" id="cancelMediaBtn">✕</span>';
        var cancelBtn = document.getElementById('cancelMediaBtn');
        cancelBtn.addEventListener('click', clearMedia);

        var valid = true;
        for (var i = 0; i < files.length; i++) {
            if (files[i].size > 10 * 1024 * 1024) { // 10MB
                alert('Ukuran file maksimal adalah 10MB (file: ' + files[i].name + ').');
                valid = false;
                break;
            }
        }

        if (!valid) {
            this.value = '';
            mediaBox.style.display = 'none';
            return;
        }

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var url = URL.createObjectURL(file);
            var item = document.createElement('div');
            item.className = 'media-preview-item';
            
            if (file.type.startsWith('image/')) {
                item.innerHTML = '<img src="' + url + '">';
            } else if (file.type.startsWith('video/')) {
                item.innerHTML = '<video src="' + url + '"></video>';
            }
            mediaBox.appendChild(item);
        }

        mediaBox.style.display = 'block';
    });

    function clearMedia() {
        mediaInput.value = '';
        mediaBox.style.display = 'none';
        mediaBox.innerHTML = '<span class="cancel-btn" id="cancelMediaBtn">✕</span>';
    }

    // === FETCH MESSAGES ===
    window.fetchMessages = function() {
        if (isFetching) return;
        isFetching = true;

        var xhr = new XMLHttpRequest();
        xhr.open('GET', '{{ route("community.chat.fetch", $community->id) }}?last_id=' + lastChatId);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function() {
            isFetching = false;
            if (xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.chats && data.chats.length > 0) {
                        if (statusEl) {
                            statusEl.remove();
                            statusEl = null;
                        }
                        var wasBottom = isAtBottom();
                        for (var i = 0; i < data.chats.length; i++) {
                            window.allChats.push(data.chats[i]);
                            window.addBubble(data.chats[i]);
                        }
                        lastChatId = data.last_id;
                        if (wasBottom) scrollDown();
                    } else if (lastChatId === 0 && statusEl) {
                        statusEl.textContent = 'Belum ada pesan. Mulai percakapan!';
                    }
                } catch(e) {
                    console.error('Parse error:', e);
                }
            }
        };
        xhr.onerror = function() { isFetching = false; };
        xhr.send();
    }

    // === SEND MESSAGE ===
    function sendMessage() {
        var pesan = inputEl.value.trim();
        var files = mediaInput.files;
        if ((!pesan && files.length === 0) || isSending) return;

        isSending = true;
        var parentId = replyIdEl.value || null;

        var formData = new FormData();
        if (pesan) formData.append('pesan', pesan);
        if (parentId) formData.append('parent_id', parentId);
        
        for (var i = 0; i < files.length; i++) {
            formData.append('media[]', files[i]);
        }

        // Clear UI immediately
        inputEl.value = '';
        clearReply();
        clearMedia();
        emojiPicker.style.display = 'none';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("community.chat.send", $community->id) }}');
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function() {
            isSending = false;
            if (xhr.status === 200 || xhr.status === 201) {
                fetchMessages(); 
            } else if (xhr.status === 419) {
                alert('Sesi habis. Halaman akan dimuat ulang.');
                window.location.reload();
            } else if (xhr.status === 422) {
                alert('File terlalu besar atau format tidak didukung.');
            } else {
                alert('Gagal kirim. Coba lagi.');
            }
        };
        xhr.onerror = function() {
            isSending = false;
            alert('Koneksi gagal.');
        };
        xhr.send(formData);
    }

    // === ADD BUBBLE ===
    window.addBubble = function(chat) {
        // If bubble already exists, update it instead of appending (for reactions update)
        var existing = document.getElementById('chat-bubble-' + chat.id);
        var isNew = !existing;
        var div = existing || document.createElement('div');
        
        if (isNew) {
            div.id = 'chat-bubble-' + chat.id;
            div.className = 'message-bubble ' + (chat.is_mine ? 'message-mine' : 'message-other');
        }

        var html = '';
        var pickerHtml = '<div class="reaction-picker">' +
            '<span class="reaction-emoji" onclick="toggleReaction('+chat.id+', \'👍\')">👍</span>' +
            '<span class="reaction-emoji" onclick="toggleReaction('+chat.id+', \'❤️\')">❤️</span>' +
            '<span class="reaction-emoji" onclick="toggleReaction('+chat.id+', \'😂\')">😂</span>' +
            '<span class="reaction-emoji" onclick="toggleReaction('+chat.id+', \'😮\')">😮</span>' +
            '<span class="reaction-emoji" onclick="toggleReaction('+chat.id+', \'😢\')">😢</span>' +
            '<span class="reaction-emoji" onclick="toggleReaction('+chat.id+', \'👏\')">👏</span>' +
            '</div>';
        
        html += pickerHtml;
        if (!chat.is_mine) {
            html += '<div class="message-sender">' + esc(chat.username) + '</div>';
        }
        if (chat.parent) {
            html += '<div class="message-reply-preview">';
            html += '<div style="font-weight:bold;font-size:0.75rem;">' + esc(chat.parent.username) + '</div>';
            html += '<div style="color:var(--text-secondary);">' + esc(chat.parent.pesan) + '</div>';
            html += '</div>';
        }
        
        // Media Attachment
        if (chat.media && chat.media.length > 0) {
            html += '<div class="chat-media-grid count-1">';
            var mediaItem = chat.media[0];
            var extraCount = chat.media.length - 1;
            
            html += '<div class="media-item-container single-preview" onclick="openLightbox(' + chat.id + ', 0)">';
            
            if (mediaItem.type === 'video') {
                html += '<video src="' + mediaItem.url + '" preload="metadata" style="pointer-events:none;"></video>';
                html += '<div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:rgba(0,0,0,0.5);border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;color:white;font-size:20px;pointer-events:none;">▶</div>';
            } else {
                html += '<img src="' + mediaItem.url + '">';
            }

            if (extraCount > 0) {
                html += '<div class="media-overlay">+' + extraCount + '</div>';
            }
            html += '</div>';
            html += '</div>';
        }

        if (chat.pesan) {
            html += '<div style="color:var(--text-primary);white-space:pre-wrap;">' + esc(chat.pesan) + '</div>';
        }
        
        html += '<div class="message-time">' + chat.waktu + '</div>';
        
        // Reactions
        if (chat.reactions && Object.keys(chat.reactions).length > 0) {
            html += '<div class="reactions-container">';
            var currentUserId = {{ auth()->id() }};
            for (var emoji in chat.reactions) {
                var users = chat.reactions[emoji];
                var count = users.length;
                var userReacted = users.includes(currentUserId);
                var activeClass = userReacted ? 'user-reacted' : '';
                html += '<div class="reaction-badge '+activeClass+'" onclick="toggleReaction('+chat.id+', \''+emoji+'\')">' + emoji + ' ' + count + '</div>';
            }
            html += '</div>';
        }

        html += '<button class="reply-btn" data-id="' + chat.id + '" data-user="' + esc(chat.username) + '" data-msg="' + esc(chat.pesan) + '">↩️</button>';

        div.innerHTML = html;
        if (isNew) {
            messagesEl.appendChild(div);
        }

        // Attach reply click
        var replyBtn = div.querySelector('.reply-btn');
        if (replyBtn) {
            replyBtn.addEventListener('click', function() {
                setReply(this.dataset.id, this.dataset.user, this.dataset.msg || 'Media');
            });
        }
    }

    // === REPLY ===
    function setReply(id, username, msg) {
        replyIdEl.value = id;
        replyUsernameEl.textContent = username;
        replyTextEl.textContent = msg.substring(0, 60);
        replyBox.style.display = 'block';
        inputEl.focus();
    }

    function clearReply() {
        replyIdEl.value = '';
        replyBox.style.display = 'none';
    }

    // === HELPERS ===
    function isAtBottom() {
        return messagesEl.scrollHeight - messagesEl.clientHeight <= messagesEl.scrollTop + 60;
    }

    function scrollDown() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function esc(str) {
        if (!str) return '';
        return str.toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // === EVENT LISTENERS ===
    sendBtn.addEventListener('click', sendMessage);

    inputEl.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    cancelReplyBtn.addEventListener('click', clearReply);

    // === START ===
    window.fetchMessages();
    setTimeout(scrollDown, 800);
    setInterval(window.fetchMessages, 2000);

})();

// === GLOBAL LIGHTBOX FUNCTIONS ===
window.currentLightboxMedia = [];
window.currentLightboxIndex = 0;

window.openLightbox = function(chatId, index) {
    var chat = window.allChats.find(c => c.id == chatId);
    if(!chat || !chat.media) return;
    window.currentLightboxMedia = chat.media;
    window.currentLightboxIndex = index;
    window.renderLightboxMedia();
    document.getElementById('lightboxModal').style.display = 'block';
}

window.closeLightbox = function() {
    document.getElementById('lightboxModal').style.display = 'none';
    document.getElementById('lightboxContent').innerHTML = ''; // Stop video
}

window.changeLightbox = function(dir) {
    window.currentLightboxIndex += dir;
    if(window.currentLightboxIndex >= window.currentLightboxMedia.length) window.currentLightboxIndex = 0;
    if(window.currentLightboxIndex < 0) window.currentLightboxIndex = window.currentLightboxMedia.length - 1;
    window.renderLightboxMedia();
}

window.renderLightboxMedia = function() {
    var media = window.currentLightboxMedia[window.currentLightboxIndex];
    var content = document.getElementById('lightboxContent');
    if(media.type === 'video') {
        content.innerHTML = '<video class="lightbox-media" src="'+media.url+'" controls autoplay></video>';
    } else {
        content.innerHTML = '<img class="lightbox-media" src="'+media.url+'">';
    }
}

window.toggleReaction = function(chatId, emoji) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ url("/community") }}/{{ $community->id }}/chat/'+chatId+'/react');
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function() {
        if (xhr.status === 200) {
            var data = JSON.parse(xhr.responseText);
            if (data.success) {
                var chat = window.allChats.find(c => c.id == chatId);
                if (chat) {
                    chat.reactions = data.reactions;
                    if (typeof window.addBubble === 'function') {
                        window.addBubble(chat);
                    }
                }
            }
        } else {
            console.error(xhr.responseText);
        }
    };
    xhr.send(JSON.stringify({ emoji: emoji }));
}
</script>
@endsection

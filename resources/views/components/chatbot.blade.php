<div id="chatbot" class="fixed bottom-4 left-4 z-40">
    <button id="chatToggle" class="btn-primary rounded-full px-4 py-3">Chat</button>
    <div id="chatWindow" class="hidden mt-2 w-80 rounded-2xl bg-white p-3 shadow-2xl ring-1 ring-black/5">
        <div class="flex items-center justify-between">
            <h4 class="font-semibold text-[--color-brand-deepblue]">Support</h4>
            <button id="chatClose" class="p-1 text-slate-500">✕</button>
        </div>
        <div id="chatMessages" class="mt-3 max-h-96 overflow-y-auto space-y-2 text-sm"></div>
        <div id="chatAgentOffer" class="mt-3 hidden">
            <div class="rounded-lg bg-yellow-50 p-2 text-xs text-yellow-800">An agent is available now.</div>
            <div class="mt-2 flex gap-2">
                <button id="acceptAgentBtn" class="btn-primary flex-1">Connect to agent</button>
            </div>
        </div>
        <div id="chatContact" class="mt-3 hidden space-y-2">
            <input id="contactName" class="w-full rounded-lg border border-slate-300 px-3 py-2"
                placeholder="Your name (optional)" />
            <input id="contactEmail" type="email" class="w-full rounded-lg border border-slate-300 px-3 py-2"
                placeholder="Email (optional)" />
            <input id="contactPhone" class="w-full rounded-lg border border-slate-300 px-3 py-2"
                placeholder="Phone (optional)" />
            <button id="saveContactBtn" class="btn-primary w-full">Save contact</button>
        </div>
        <div id="chatMenu" class="mt-3 hidden space-y-2">
            <button data-choice="order-status" class="w-full rounded-lg bg-slate-100 px-3 py-2 text-left">Order
                status</button>
            <button data-choice="quote" class="w-full rounded-lg bg-slate-100 px-3 py-2 text-left">Request a
                quote</button>
            <button data-choice="pricing" class="w-full rounded-lg bg-slate-100 px-3 py-2 text-left">Pricing
                info</button>
            <button data-choice="upload" class="w-full rounded-lg bg-slate-100 px-3 py-2 text-left">Upload
                files</button>
            <button data-choice="message" class="w-full rounded-lg bg-slate-100 px-3 py-2 text-left">Leave a
                message</button>
        </div>
        <form id="chatForm" class="mt-3 flex gap-2">
            <input id="chatInput" class="flex-1 rounded-lg border border-slate-300 px-3 py-2"
                placeholder="Type here..." />
            <input type="file" id="chatFile" class="hidden" />
            <button type="button" id="chatUpload" class="rounded-lg border px-2">📎</button>
            <button class="btn-primary">Send</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const chatToggle = document.getElementById('chatToggle');
        const chatClose = document.getElementById('chatClose');
        const chatWindow = document.getElementById('chatWindow');
        const chatMessages = document.getElementById('chatMessages');
        const chatMenu = document.getElementById('chatMenu');
        const chatAgentOffer = document.getElementById('chatAgentOffer');
        const acceptAgentBtn = document.getElementById('acceptAgentBtn');
        const chatContact = document.getElementById('chatContact');
        const contactName = document.getElementById('contactName');
        const contactEmail = document.getElementById('contactEmail');
        const contactPhone = document.getElementById('contactPhone');
        const saveContactBtn = document.getElementById('saveContactBtn');
        const chatForm = document.getElementById('chatForm');
        const chatInput = document.getElementById('chatInput');
        const chatFile = document.getElementById('chatFile');
        const chatUpload = document.getElementById('chatUpload');

        let sessionId = null;
        let lastMessageId = 0;
        let polling = null;
        let settings = {
            is_open_now: false
        };

        function addMessage(text, who = 'bot') {
            const row = document.createElement('div');
            row.className = who === 'bot' ? 'text-slate-700' : 'text-[--color-brand-deepblue] text-right';
            row.textContent = text;
            chatMessages.appendChild(row);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        async function fetchSettings() {
            try {
                const res = await fetch('{{ route('chatbot.settings') }}');
                settings = await res.json();
            } catch (e) {
                /* ignore */ }
        }

        async function startSession() {
            await fetchSettings();
            const res = await fetch('{{ route('chatbot.start') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const data = await res.json();
            sessionId = data.session_id;
            addMessage('Hi! How can I help you today?');
            chatMenu.classList.remove('hidden');
            startPolling();
        }

        // Avoid clashing with floating live chat: if user is authenticated and floating chat exists, this remains manual only
        chatToggle.addEventListener('click', () => {
            chatWindow.classList.toggle('hidden');
            if (!sessionId) startSession();
        });
        chatClose.addEventListener('click', () => chatWindow.classList.add('hidden'));

        chatMenu.addEventListener('click', async (e) => {
            if (!e.target.dataset.choice) return;
            const choice = e.target.dataset.choice;
            await fetch(`{{ url('chatbot') }}/${sessionId}/menu`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    choice
                })
            });
            addMessage('You chose: ' + e.target.textContent, 'user');
            if (choice === 'message') {
                chatContact.classList.remove('hidden');
            }
        });

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const text = chatInput.value.trim();
            if (!text || !sessionId) return;
            await fetch(`{{ url('chatbot') }}/${sessionId}/message`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message: text
                })
            });
            addMessage(text, 'user');
            chatInput.value = '';
        });

        chatUpload.addEventListener('click', () => chatFile.click());
        chatFile.addEventListener('change', async () => {
            if (!chatFile.files.length || !sessionId) return;
            const file = chatFile.files[0];
            if (file.size > 20 * 1024 * 1024) {
                addMessage('File too large (max 20MB)');
                chatFile.value = '';
                return;
            }
            const fd = new FormData();
            fd.append('file', file);
            try {
                await fetch(`{{ url('chatbot') }}/${sessionId}/upload`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: fd
                });
                addMessage('Uploaded: ' + file.name, 'user');
            } catch (e) {
                addMessage('Upload failed');
            }
            chatFile.value = '';
        });

        saveContactBtn.addEventListener('click', async () => {
            if (!sessionId) return;
            const body = {
                name: contactName.value,
                email: contactEmail.value,
                phone: contactPhone.value
            };
            await fetch(`{{ url('chatbot') }}/${sessionId}/contact`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(body)
            });
            addMessage('Contact details saved.');
            chatContact.classList.add('hidden');
        });

        acceptAgentBtn.addEventListener('click', async () => {
            if (!sessionId) return;
            await fetch(`{{ url('chatbot') }}/${sessionId}/accept-agent`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            chatAgentOffer.classList.add('hidden');
            addMessage('Connecting you to a live agent...');
        });

        function startPolling() {
            if (polling) return;
            polling = setInterval(async () => {
                if (!sessionId) return;
                try {
                    const res = await fetch(
                        `{{ url('chatbot') }}/${sessionId}/poll?since_id=${lastMessageId}`);
                    const data = await res.json();
                    (data.messages || []).forEach(msg => {
                        addMessage(msg.message || '', msg.sender);
                        lastMessageId = Math.max(lastMessageId, msg.id);
                    });
                    // Agent availability handling
                    if (data.session && data.session.agent_offered && data.session.status !==
                        'live') {
                        chatAgentOffer.classList.remove('hidden');
                    } else {
                        chatAgentOffer.classList.add('hidden');
                    }
                } catch (e) {
                    /* ignore */ }
            }, 3000);
        }
    });
</script>

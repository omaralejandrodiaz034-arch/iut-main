window.notificacionesData = function() {
    return {
        open: false,
        unreadCount: 0,
        ultimas: [],
        init() {
            const el = this.$el;
            this.unreadCount = parseInt(el.dataset.unreadCount || '0', 10);
            this.ultimas = JSON.parse(el.dataset.notificaciones || '[]');

            const url = el.dataset.url;
            if (!url) return;

            const self = this;
            setInterval(() => {
                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data && typeof data.count !== 'undefined') {
                        self.unreadCount = data.count;
                        self.ultimas = data.ultimas || [];
                    }
                })
                .catch(() => {});
            }, 30000);
        },
        marcarYRedirigir(n) {
            if (! n || ! n.id) return;
            
            const destino = n.action_url || n.url;
            if (!destino) return;

            fetch(`/notificaciones/${n.id}/ir`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(() => {
                window.location.href = destino;
            })
            .catch(() => {
                window.location.href = destino;
            });
        },
        eliminar(n) {
            if (! n || ! n.eliminar_url) return;

            fetch(n.eliminar_url, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(r => r.json())
            .then(() => {
                this.ultimas = this.ultimas.filter(item => item.id !== n.id);
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            })
            .catch(() => {});
        }
    };
};

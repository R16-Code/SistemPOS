/**
 * BrewPOS Real-time Connection & Offline Resilience Manager
 * Handles WebSocket connection states (Echo / Reverb / Pusher),
 * browser network online/offline events, auto-reconnect, and fallback polling.
 */

class BrewRealtimeManager {
    constructor() {
        this.state = navigator.onLine ? 'online' : 'offline';
        this.mode = 'polling'; // 'websocket' or 'polling'
        this.socketDisconnectedTimer = null;
        this.reconnectAttempts = 0;
        this.listeners = [];

        this.init();
    }

    init() {
        // 1. Browser Network State Listeners
        window.addEventListener('online', () => {
            this.handleOnline();
        });

        window.addEventListener('offline', () => {
            this.handleOffline();
        });

        // 2. Tab Visibility Change (instant catch-up when cashier tabs back)
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible' && navigator.onLine) {
                this.triggerLivewireSync();
            }
        });

        // 3. Check for Echo / WebSocket integration
        this.initWebSocketListeners();

        // 4. Expose globally
        window.BrewRealtime = this;
    }

    initWebSocketListeners() {
        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
            const pusher = window.Echo.connector.pusher;
            this.mode = 'websocket';

            pusher.connection.bind('connected', () => {
                if (this.socketDisconnectedTimer) {
                    clearTimeout(this.socketDisconnectedTimer);
                    this.socketDisconnectedTimer = null;
                }
                this.setState('online');
                this.mode = 'websocket';
            });

            pusher.connection.bind('disconnected', () => {
                this.handleSocketDisconnect();
            });

            pusher.connection.bind('unavailable', () => {
                this.handleSocketDisconnect();
            });

            pusher.connection.bind('failed', () => {
                this.handleSocketDisconnect();
            });
        } else {
            // No WebSocket driver installed/configured, active fallback polling mode
            this.mode = 'fallback_polling';
        }
    }

    handleSocketDisconnect() {
        this.setState('reconnecting');

        // Fallback to active polling if WebSocket fails to reconnect within 15 seconds
        if (!this.socketDisconnectedTimer) {
            this.socketDisconnectedTimer = setTimeout(() => {
                if (this.state !== 'online') {
                    console.warn('[BrewPOS Realtime] WebSocket connection timed out (>15s). Switched to Fallback HTTP Polling.');
                    this.mode = 'fallback_polling';
                    this.setState(navigator.onLine ? 'online' : 'offline');
                    this.triggerLivewireSync();
                }
            }, 15000);
        }
    }

    handleOnline() {
        console.log('[BrewPOS Realtime] Network restored. Auto-reconnecting...');
        this.setState('online');
        this.reconnectAttempts = 0;

        // Auto-reconnect WebSocket if present
        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
            try {
                window.Echo.connector.pusher.connect();
            } catch (e) {
                console.error('[BrewPOS Realtime] WebSocket connect error:', e);
            }
        }

        // Trigger Livewire sync without requiring manual page reload (F5)
        this.triggerLivewireSync();
    }

    handleOffline() {
        console.warn('[BrewPOS Realtime] Network lost. System is currently offline.');
        this.setState('offline');
    }

    setState(newState) {
        if (this.state !== newState) {
            this.state = newState;
            this.notify();
            window.dispatchEvent(new CustomEvent('brew-realtime-status-changed', {
                detail: { state: this.state, mode: this.mode }
            }));
        }
    }

    subscribe(callback) {
        this.listeners.push(callback);
        callback(this.state, this.mode);
        return () => {
            this.listeners = this.listeners.filter(cb => cb !== callback);
        };
    }

    notify() {
        this.listeners.forEach(cb => {
            try {
                cb(this.state, this.mode);
            } catch (e) {
                console.error(e);
            }
        });
    }

    triggerLivewireSync() {
        if (window.Livewire) {
            try {
                // Dispatch event to all active Livewire components to refresh their state
                window.Livewire.dispatch('refresh-realtime');
            } catch (e) {
                console.error('[BrewPOS Realtime] Livewire dispatch error:', e);
            }
        }
    }
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => new BrewRealtimeManager());
} else {
    new BrewRealtimeManager();
}

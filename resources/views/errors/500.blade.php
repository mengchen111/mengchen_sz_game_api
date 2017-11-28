<div class="content">
    <div class="title">Something went wrong.</div>

    @if(app()->bound('sentry') && !empty(Sentry::getLastEventID()))
        <div class="subtitle">Error ID: {{ Sentry::getLastEventID() }}</div>

        <!-- Sentry JS SDK 2.1.+ required -->
        <script src="https://cdn.ravenjs.com/3.3.0/raven.min.js"></script>

        <script>
          Raven.showReportDialog({
            eventId: '{{ Sentry::getLastEventID() }}',
            // use the public DSN (dont include your secret!)
            dsn: 'https://e164d7b4074e460c8fcdce8d00eae7a5@sentry.io/251245',
            user: {
              'name': '刘点',
              'email': '408374313@qq.com',
            }
          });
        </script>
    @endif
</div>
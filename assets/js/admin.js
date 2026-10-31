jQuery(document).ready(function($) {
    if (window.location.href.indexOf('page=kognetiks-ai-summaries') > -1) {
        if ($('a.nav-tab-active').text() === 'Support' || $('a.nav-tab-active').text() === 'Tools') {
            $('input[type="submit"]').hide();
        }
    }

    var config = window.kognetiksAiSummariesRefresh;
    if (!config || !config.ajaxUrl) {
        return;
    }

    var $button = $('#kognetiks-ai-summaries-refresh-all');
    var $resume = $('#kognetiks-ai-summaries-refresh-resume');
    var $status = $('#kognetiks-ai-summaries-refresh-status');
    var requestInFlight = false;
    var pollTimer = null;

    function render(state) {
        if (!state) {
            return;
        }
        var success = state.success || 0;
        var skipped = state.skipped || 0;
        var failed = state.failed || 0;
        var remaining = state.remaining || 0;
        var total = state.total || 0;
        var counts = 'Total: ' + total + ', successful: ' + success + ', skipped: ' + skipped + ', failed: ' + failed + ', remaining: ' + remaining + '.';
        var text = '';
        if (state.start_error) {
            text = state.start_error;
        } else if (state.status === 'running') {
            text = 'Refresh running. ' + counts;
        } else if (state.status === 'paused') {
            text = 'Refresh paused: ' + (state.reason || 'administrator action is required') + ' ' + counts;
        } else if (state.status === 'complete') {
            text = 'Refresh completed. ' + counts;
        }
        if (text) {
            $status.text(text);
        }
        $resume.prop('hidden', state.status !== 'paused');
        $button.prop('disabled', state.status === 'running');
    }

    function postStep(step) {
        return $.post(config.ajaxUrl, {
            action: 'kognetiks_ai_summaries_refresh_batch',
            nonce: config.nonce,
            step: step
        });
    }

    function schedulePoll(state) {
        window.clearTimeout(pollTimer);
        if (!state || (state.status !== 'running' && state.status !== 'paused')) {
            return;
        }
        pollTimer = window.setTimeout(pollStatus, 5000);
    }

    function pollStatus() {
        postStep('status').done(function(response) {
            var state = response && response.data ? response.data : null;
            if (!response || !response.success || !state) {
                return;
            }
            render(state);
            schedulePoll(state);
        });
    }

    $button.on('click', function(event) {
        event.preventDefault();
        if (requestInFlight) {
            return;
        }
        if (!window.confirm('Are you sure you want to refresh ALL AI summaries? This may take a long time.')) {
            return;
        }
        requestInFlight = true;
        $button.prop('disabled', true);
        postStep('start').done(function(response) {
            requestInFlight = false;
            var state = response && response.data ? response.data : null;
            if (!response || !response.success || !state) {
                $status.text('Could not start the refresh.');
                $button.prop('disabled', false);
                return;
            }
            render(state);
            schedulePoll(state);
        }).fail(function() {
            requestInFlight = false;
            $status.text('Could not start the refresh. The run was not marked complete.');
            $button.prop('disabled', false);
        });
    });

    $resume.on('click', function(event) {
        event.preventDefault();
        if (requestInFlight) {
            return;
        }
        requestInFlight = true;
        postStep('resume').done(function(response) {
            requestInFlight = false;
            var state = response && response.data ? response.data : null;
            if (!response || !response.success || !state) {
                $status.text('Could not resume the refresh.');
                return;
            }
            render(state);
            schedulePoll(state);
        }).fail(function() {
            requestInFlight = false;
            $status.text('Could not resume the refresh.');
        });
    });

    if (config.state && (config.state.status === 'running' || config.state.status === 'paused' || config.state.status === 'complete')) {
        render(config.state);
        schedulePoll(config.state);
    }
});

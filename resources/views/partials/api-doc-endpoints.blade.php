@foreach($sections as $sectionKey => $section)
    <div class="tab-pane fade" id="tab-{{ $sectionKey }}">
        <div class="card api-card">
            <div class="card-body p-4">
                @if(!empty($section['label']))
                    <h5 class="fw-bold mb-2">{{ $section['label'] }}</h5>
                @endif
                @if(!empty($section['intro']))
                    <p class="text-muted small mb-3">{{ $section['intro'] }}</p>
                @endif

                @if(!empty($section['workflow']))
                    <div class="alert alert-light border small mb-4">
                        <div class="fw-semibold mb-2">How to buy</div>
                        <ol class="mb-0 ps-3">
                            @foreach($section['workflow'] as $step)
                                <li class="mb-1">{{ $step }}</li>
                            @endforeach
                        </ol>
                    </div>
                @endif

                @foreach($section['endpoints'] as $endpoint)
                    <details class="api-endpoint" @if($loop->first) open @endif>
                        <summary>
                            <span class="api-pill {{ str_contains($endpoint['method'], 'POST') ? 'api-pill-post' : 'api-pill-get' }}">{{ $endpoint['method'] }}</span>
                            <code class="small">{{ $endpoint['path'] }}</code>
                            <span class="text-muted small ms-1">{{ $endpoint['title'] }}</span>
                        </summary>
                        <div class="api-endpoint-body">
                            @if(!empty($endpoint['notes']))
                                <p class="small text-muted mb-3">{{ $endpoint['notes'] }}</p>
                            @endif

                            <p class="small fw-semibold mb-1">URL</p>
                            <pre class="api-code mb-3">{{ $endpoint['url'] }}</pre>

                            <p class="small fw-semibold mb-1">Headers</p>
                            <pre class="api-code mb-3">Authorization: Bearer YOUR_API_KEY
Accept: application/json
@if(!empty($endpoint['body_json']))Content-Type: application/json
@endif</pre>

                            <p class="small fw-semibold mb-1">Request body</p>
                            @if(!empty($endpoint['body_json']))
                                <pre class="api-code mb-3">{{ $endpoint['body_json'] }}</pre>
                            @else
                                <p class="small text-muted mb-3">None — send headers only. GET requests may use query parameters where noted.</p>
                            @endif

                            <p class="small fw-semibold mb-1">cURL example</p>
                            <pre class="api-code mb-3">{{ $endpoint['curl'] }}</pre>

                            <p class="small fw-semibold mb-1">Sample response</p>
                            <pre class="api-code mb-0">{{ $endpoint['response'] }}</pre>
                        </div>
                    </details>
                @endforeach
            </div>
        </div>
    </div>
@endforeach

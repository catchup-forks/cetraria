{{ content() }}

<h1>Error {{ code }}</h1>

{{ error.message() }}
{% if debug %}
    <br>in {{ error.file() }} on line {{ error.line() }}<br>
    {% if error.isException() %}
        <pre>{{ error.exception().getTraceAsString() }}</pre>
    {% endif %}
{% endif %}

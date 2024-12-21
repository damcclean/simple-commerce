<?php

namespace DuncanMcClean\SimpleCommerce\Tags\Concerns;

use Illuminate\Support\Str;
use Statamic\Tags\Concerns\RendersForms;

trait FormBuilder
{
    use RendersForms;

    private static array $knownParams = ['redirect', 'error_redirect', 'line_item', 'product', 'variant'];

    protected function createForm(
        string $action,
        array $data = [],
        string $method = 'POST',
        array $knownParams = []
    ): string|array {
        $knownParams = array_merge(static::$knownParams, $knownParams);

        if (! $this->parser) {
            $attrs = $this->formAttrs($action, $method, $knownParams);
            $params = $this->formParams($method, [
                'redirect' => $this->redirectValue(),
                'error_redirect' => $this->errorRedirectValue(),
            ]);

            return array_merge([
                'attrs' => $attrs,
                'attrs_html' => $this->renderAttributes($attrs),
                'params' => $this->formMetaPrefix($params),
                'params_html' => $this->formMetaFields($params),
            ], $data);
        }

        $html = $this->formOpen($action, $method, $knownParams);

        $html .= $this->redirectField();
        $html .= $this->errorRedirectField();

        $html .= $this->parse($this->sessionData($data));

        $html .= $this->formClose();

        return $html;
    }

    protected function sessionData($data = [])
    {
        if ($errors = $this->errors()) {
            $data['errors'] = $errors;
        }

        return $data;
    }

    private function redirectValue()
    {
        $redirectUrl = $this->params->get('redirect', request()->path());

        if (! $this->isExternalUrl($redirectUrl)) {
            $redirectUrl = Str::start($redirectUrl, '/');
        }

        return $redirectUrl;
    }

    private function errorRedirectValue()
    {
        $errorRedirectUrl = $this->params->get('error_redirect', request()->path());

        if (! $this->isExternalUrl($errorRedirectUrl)) {
            $errorRedirectUrl = Str::start($errorRedirectUrl, '/');
        }

        return $errorRedirectUrl;
    }

    private function redirectField()
    {
        return '<input type="hidden" name="_redirect" value="'.$this->redirectValue().'" />';
    }

    private function errorRedirectField()
    {
        return '<input type="hidden" name="_error_redirect" value="'.$this->errorRedirectValue().'" />';
    }

    /**
     * @return bool|string
     */
    public function errors()
    {
        if (! $this->hasErrors()) {
            return false;
        }

        $errors = [];

        foreach (session('errors')->getBag('default')->all() as $error) {
            $errors[]['value'] = $error;
        }

        return ($this->content === '')    // If this is a single tag...
            ? ! empty($errors)             // just output a boolean.
            : $errors;  // Otherwise, parse the content loop.
    }

    /**
     * Does this form have errors?
     */
    private function hasErrors(): bool
    {
        return (session()->has('errors'))
            ? session('errors')->hasBag('default')
            : false;
    }

    /**
     * Get the errorBag from session.
     *
     * @return object
     */
    private function getErrorBag()
    {
        if ($this->hasErrors()) {
            return session('errors')->getBag('default');
        }
    }

    protected function isExternalUrl(string $url): bool
    {
        return Str::startsWith($url, ['http://', 'https://']);
    }
}

import React from "react";

interface BadgeProps extends React.HTMLAttributes<HTMLSpanElement> {
  variant?: "info" | "warning" | "danger" | "success";
  children: React.ReactNode;
}

export const Badge: React.FC<BadgeProps> = ({
  variant = "info",
  className = "",
  children,
  style,
  ...props
}) => {
  return (
    <span className={`badge badge-${variant} ${className}`} style={style} {...props}>
      {children}
    </span>
  );
};
